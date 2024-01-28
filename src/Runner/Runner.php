<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Runner;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FileReader;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\Process;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Runner\Parallel\ProcessPool;
use PhpCsFixer\Tokenizer\Tokens;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Greg Korba <greg@codito.dev>
 *
 * @phpstan-type _RunResult array<string, array{appliedFixers: list<string>, diff: string}>
 *
 * @internal
 */
final class Runner
{
    private RunnerConfig $runnerConfig;

    private DifferInterface $differ;

    private ?DirectoryInterface $directory;

    private ?EventDispatcherInterface $eventDispatcher;

    private ErrorsManager $errorsManager;

    private CacheManagerInterface $cacheManager;

    private LinterInterface $linter;

    /**
     * @var ?iterable<\SplFileInfo>
     */
    private $fileIterator;

    private int $fileCount;

    /**
     * @var list<FixerInterface>
     */
    private array $fixers;

    /**
     * @param null|iterable<\SplFileInfo> $fileIterator
     * @param list<FixerInterface>        $fixers
     */
    public function __construct(
        RunnerConfig $runnerConfig,
        ?iterable $fileIterator,
        array $fixers,
        DifferInterface $differ,
        ?EventDispatcherInterface $eventDispatcher,
        ErrorsManager $errorsManager,
        LinterInterface $linter,
        CacheManagerInterface $cacheManager,
        ?DirectoryInterface $directory = null
    ) {
        $this->runnerConfig = $runnerConfig;
        $this->fileIterator = $fileIterator;
        $this->fixers = $fixers;
        $this->differ = $differ;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorsManager = $errorsManager;
        $this->linter = $linter;
        $this->cacheManager = $cacheManager;
        $this->directory = $directory ?? new Directory('');
    }

    /**
     * @param iterable<\SplFileInfo> $fileIterator
     */
    public function setFileIterator(iterable $fileIterator): void
    {
        $this->fileIterator = $fileIterator;
    }

    /**
     * @return _RunResult
     */
    public function fix(): array
    {
        return $this->runnerConfig->getParallelConfig()->getMaxProcesses() > 1
            ? $this->fixParallel()
            : $this->fixSequential();
    }

    /**
     * Heavily inspired by {@see https://github.com/phpstan/phpstan-src/blob/9ce425bca5337039fb52c0acf96a20a2b8ace490/src/Parallel/ParallelAnalyser.php}.
     *
     * @return _RunResult
     */
    private function fixParallel(): array
    {
        $changed = [];
        $streamSelectLoop = new StreamSelectLoop();
        $server = new TcpServer('127.0.0.1:0', $streamSelectLoop);
        $serverPort = parse_url($server->getAddress() ?? '', PHP_URL_PORT);

        if (!is_numeric($serverPort)) {
            throw new ParallelisationException(sprintf(
                'Unable to parse server port from "%s"',
                $server->getAddress() ?? ''
            ));
        }

        $processPool = new ProcessPool($server);
        $fileIterator = $this->getFileIterator();
        $fileIterator->rewind();

        $fileChunk = function () use ($fileIterator): array {
            $files = [];

            while (\count($files) < $this->runnerConfig->getParallelConfig()->getFilesPerProcess()) {
                $current = $fileIterator->current();

                if (null === $current) {
                    break;
                }

                $files[] = $current->getRealPath();

                $fileIterator->next();
            }

            return $files;
        };

        // [REACT] Handle worker's handshake (init connection)
        $server->on('connection', static function (ConnectionInterface $connection) use ($processPool, $fileChunk): void {
            $jsonInvalidUtf8Ignore = \defined('JSON_INVALID_UTF8_IGNORE') ? JSON_INVALID_UTF8_IGNORE : 0;
            $decoder = new Decoder($connection, true, 512, $jsonInvalidUtf8Ignore);
            $encoder = new Encoder($connection, $jsonInvalidUtf8Ignore);

            // [REACT] Bind connection when worker's process requests "hello" action (enables 2-way communication)
            $decoder->on('data', static function (array $data) use ($processPool, $fileChunk, $decoder, $encoder): void {
                if ('hello' !== $data['action']) {
                    return;
                }

                $identifier = ProcessIdentifier::fromRaw($data['identifier']);
                $process = $processPool->getProcess($identifier);
                $process->bindConnection($decoder, $encoder);
                $job = $fileChunk();

                if (0 === \count($job)) {
                    $processPool->endProcessIfKnown($identifier);

                    return;
                }

                $process->request(['action' => 'run', 'files' => $job]);
            });
        });

        $processesToSpawn = min(
            $this->runnerConfig->getParallelConfig()->getMaxProcesses(),
            (int) ceil($this->fileCount / $this->runnerConfig->getParallelConfig()->getFilesPerProcess())
        );

        for ($i = 0; $i < $processesToSpawn; ++$i) {
            $identifier = ProcessIdentifier::create();
            $process = Process::create(
                $streamSelectLoop,
                $this->runnerConfig,
                $identifier,
                $serverPort,
            );
            $processPool->addProcess($identifier, $process);
            $process->start(
                // [REACT] Handle worker's "result" action (analysis report)
                function (array $analysisResult) use ($processPool, $process, $identifier, $fileChunk, &$changed): void {
                    foreach ($analysisResult as $file => $result) {
                        // Pass-back information about applied changes (only if there are any)
                        if (isset($result['fixInfo'])) {
                            $changed[$file] = $result['fixInfo'];
                        }
                        // Dispatch an event for each file processed and dispatch its status (required for progress output)
                        $this->dispatchEvent(FixerFileProcessedEvent::NAME, new FixerFileProcessedEvent($result['status']));

                        foreach ($result['errors'] ?? [] as $workerError) {
                            $error = new Error(
                                $workerError['type'],
                                $workerError['filePath'],
                                null !== $workerError['source']
                                    ? ParallelisationException::forWorkerError($workerError['source'])
                                    : null,
                                $workerError['appliedFixers'],
                                $workerError['diff']
                            );

                            $this->errorsManager->report($error);
                        }
                    }

                    // Request another chunk of files, if still available
                    $job = $fileChunk();

                    if (0 === \count($job)) {
                        $processPool->endProcessIfKnown($identifier);

                        return;
                    }

                    $process->request(['action' => 'run', 'files' => $job]);
                },

                // [REACT] Handle errors encountered during worker's execution
                static function (\Throwable $error) use ($processPool): void {
                    // @TODO Pass-back error to the main process so it can be displayed to the user

                    $processPool->endAll();
                },

                // [REACT] Handle worker's shutdown
                static function ($exitCode, string $output) use ($processPool, $identifier): void {
                    $processPool->endProcessIfKnown($identifier);

                    if (0 === $exitCode || null === $exitCode) {
                        return;
                    }

                    // @TODO Handle output string for non-zero exit codes
                }
            );
        }

        $streamSelectLoop->run();

        return $changed;
    }

    /**
     * @return _RunResult
     */
    private function fixSequential(): array
    {
        $changed = [];
        $collection = $this->getFileIterator();

        foreach ($collection as $file) {
            $fixInfo = $this->fixFile($file, $collection->currentLintingResult());

            // we do not need Tokens to still caching just fixed file - so clear the cache
            Tokens::clearCache();

            if (null !== $fixInfo) {
                $name = $this->directory->getRelativePathTo($file->__toString());
                $changed[$name] = $fixInfo;

                if ($this->runnerConfig->shouldStopOnViolation()) {
                    break;
                }
            }
        }

        return $changed;
    }

    /**
     * @return null|array{appliedFixers: list<string>, diff: string}
     */
    private function fixFile(\SplFileInfo $file, LintingResultInterface $lintingResult): ?array
    {
        $name = $file->getPathname();

        try {
            $lintingResult->check();
        } catch (LintingException $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                new FixerFileProcessedEvent(FixerFileProcessedEvent::STATUS_INVALID)
            );

            $this->errorsManager->report(new Error(Error::TYPE_INVALID, $name, $e));

            return null;
        }

        $old = FileReader::createSingleton()->read($file->getRealPath());

        $tokens = Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();

        $new = $old;
        $newHash = $oldHash;

        $appliedFixers = [];

        try {
            foreach ($this->fixers as $fixer) {
                // for custom fixers we don't know is it safe to run `->fix()` without checking `->supports()` and `->isCandidate()`,
                // thus we need to check it and conditionally skip fixing
                if (
                    !$fixer instanceof AbstractFixer
                    && (!$fixer->supports($file) || !$fixer->isCandidate($tokens))
                ) {
                    continue;
                }

                $fixer->fix($file, $tokens);

                if ($tokens->isChanged()) {
                    $tokens->clearEmptyTokens();
                    $tokens->clearChanged();
                    $appliedFixers[] = $fixer->getName();
                }
            }
        } catch (\ParseError $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                new FixerFileProcessedEvent(FixerFileProcessedEvent::STATUS_LINT)
            );

            $this->errorsManager->report(new Error(Error::TYPE_LINT, $name, $e));

            return null;
        } catch (\Throwable $e) {
            $this->processException($name, $e);

            return null;
        }

        $fixInfo = null;

        if ([] !== $appliedFixers) {
            $new = $tokens->generateCode();
            $newHash = $tokens->getCodeHash();
        }

        // We need to check if content was changed and then applied changes.
        // But we can't simply check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($oldHash !== $newHash) {
            $fixInfo = [
                'appliedFixers' => $appliedFixers,
                'diff' => $this->differ->diff($old, $new, $file),
            ];

            try {
                $this->linter->lintSource($new)->check();
            } catch (LintingException $e) {
                $this->dispatchEvent(
                    FixerFileProcessedEvent::NAME,
                    new FixerFileProcessedEvent(FixerFileProcessedEvent::STATUS_LINT)
                );

                $this->errorsManager->report(new Error(Error::TYPE_LINT, $name, $e, $fixInfo['appliedFixers'], $fixInfo['diff']));

                return null;
            }

            if (!$this->runnerConfig->isDryRun()) {
                $fileName = $file->getRealPath();

                if (!file_exists($fileName)) {
                    throw new IOException(
                        sprintf('Failed to write file "%s" (no longer) exists.', $file->getPathname()),
                        0,
                        null,
                        $file->getPathname()
                    );
                }

                if (is_dir($fileName)) {
                    throw new IOException(
                        sprintf('Cannot write file "%s" as the location exists as directory.', $fileName),
                        0,
                        null,
                        $fileName
                    );
                }

                if (!is_writable($fileName)) {
                    throw new IOException(
                        sprintf('Cannot write to file "%s" as it is not writable.', $fileName),
                        0,
                        null,
                        $fileName
                    );
                }

                if (false === @file_put_contents($fileName, $new)) {
                    $error = error_get_last();

                    throw new IOException(
                        sprintf('Failed to write file "%s", "%s".', $fileName, null !== $error ? $error['message'] : 'no reason available'),
                        0,
                        null,
                        $fileName
                    );
                }
            }
        }

        $this->cacheManager->setFileHash($name, $newHash);

        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            new FixerFileProcessedEvent(null !== $fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES)
        );

        return $fixInfo;
    }

    /**
     * Process an exception that occurred.
     */
    private function processException(string $name, \Throwable $e): void
    {
        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            new FixerFileProcessedEvent(FixerFileProcessedEvent::STATUS_EXCEPTION)
        );

        $this->errorsManager->report(new Error(Error::TYPE_EXCEPTION, $name, $e));
    }

    private function dispatchEvent(string $name, Event $event): void
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($event, $name);
    }

    private function getFileIterator(): LintingResultAwareFileIteratorInterface
    {
        if (null === $this->fileIterator) {
            throw new \RuntimeException('File iterator is not configured. Pass paths during Runner initialisation or set them after with `setFileIterator()`.');
        }

        $fileIterator = new \ArrayIterator(
            $this->fileIterator instanceof \IteratorAggregate
                ? $this->fileIterator->getIterator()
                : $this->fileIterator
        );

        // In order to determine the amount of required workers, we need to know how many files we need to analyse
        $this->fileCount = \count(iterator_to_array($fileIterator));
        $fileIterator->rewind(); // Important! Without this 0 files would be analysed

        $fileFilterIterator = new FileFilterIterator(
            $fileIterator,
            $this->eventDispatcher,
            $this->cacheManager
        );

        return $this->linter->isAsync()
            ? new FileCachingLintingFileIterator($fileFilterIterator, $this->linter)
            : new LintingFileIterator($fileFilterIterator, $this->linter);
    }
}
