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
use PhpCsFixer\Console\Command\WorkerCommand;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Error\SourceExceptionFactory;
use PhpCsFixer\FileReader;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Runner\Parallel\ParallelAction;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\ProcessFactory;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Runner\Parallel\ProcessPool;
use PhpCsFixer\Runner\Parallel\WorkerException;
use PhpCsFixer\Tokenizer\Tokens;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Greg Korba <greg@codito.dev>
 *
 * @phpstan-type _RunResult array<string, array{appliedFixers: list<string>, diff: string}>
 */
final class Runner
{
    private DifferInterface $differ;

    private ?DirectoryInterface $directory;

    private ?EventDispatcherInterface $eventDispatcher;

    private ErrorsManager $errorsManager;

    private CacheManagerInterface $cacheManager;

    private bool $isDryRun;

    private LinterInterface $linter;

    /**
     * @var null|\Traversable<array-key, \SplFileInfo>
     */
    private $fileIterator;

    private int $fileCount;

    /**
     * @var list<FixerInterface>
     */
    private array $fixers;

    private bool $stopOnViolation;

    private ParallelConfig $parallelConfig;

    private ?InputInterface $input;

    private ?string $configFile;

    /**
     * @param null|\Traversable<array-key, \SplFileInfo> $fileIterator
     * @param list<FixerInterface>                       $fixers
     */
    public function __construct(
        ?\Traversable $fileIterator,
        array $fixers,
        DifferInterface $differ,
        ?EventDispatcherInterface $eventDispatcher,
        ErrorsManager $errorsManager,
        LinterInterface $linter,
        bool $isDryRun,
        CacheManagerInterface $cacheManager,
        ?DirectoryInterface $directory = null,
        bool $stopOnViolation = false,
        // @TODO Make these arguments required in 4.0
        ?ParallelConfig $parallelConfig = null,
        ?InputInterface $input = null,
        ?string $configFile = null
    ) {
        // Required only for main process (calculating workers count)
        $this->fileCount = null !== $fileIterator ? \count(iterator_to_array($fileIterator)) : 0;

        $this->fileIterator = $fileIterator;
        $this->fixers = $fixers;
        $this->differ = $differ;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorsManager = $errorsManager;
        $this->linter = $linter;
        $this->isDryRun = $isDryRun;
        $this->cacheManager = $cacheManager;
        $this->directory = $directory ?? new Directory('');
        $this->stopOnViolation = $stopOnViolation;
        $this->parallelConfig = $parallelConfig ?? ParallelConfigFactory::sequential();
        $this->input = $input;
        $this->configFile = $configFile;
    }

    /**
     * @TODO consider to drop this method and make iterator parameter obligatory in constructor,
     * more in https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/7777/files#r1590447581
     *
     * @param \Traversable<array-key, \SplFileInfo> $fileIterator
     */
    public function setFileIterator(iterable $fileIterator): void
    {
        $this->fileIterator = $fileIterator;

        // Required only for main process (calculating workers count)
        $this->fileCount = \count(iterator_to_array($fileIterator));
    }

    /**
     * @return _RunResult
     */
    public function fix(): array
    {
        if (0 === $this->fileCount) {
            return [];
        }

        // @TODO Remove condition for the input argument in 4.0, as it should be required in the constructor
        return $this->parallelConfig->getMaxProcesses() > 1 && null !== $this->input
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
        $maxFilesPerProcess = $this->parallelConfig->getFilesPerProcess();
        $fileIterator = $this->getFilteringFileIterator();
        $fileIterator->rewind();

        $getFileChunk = static function () use ($fileIterator, $maxFilesPerProcess): array {
            $files = [];

            while (\count($files) < $maxFilesPerProcess) {
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
        $server->on('connection', static function (ConnectionInterface $connection) use ($processPool, $getFileChunk): void {
            $jsonInvalidUtf8Ignore = \defined('JSON_INVALID_UTF8_IGNORE') ? JSON_INVALID_UTF8_IGNORE : 0;
            $decoder = new Decoder($connection, true, 512, $jsonInvalidUtf8Ignore);
            $encoder = new Encoder($connection, $jsonInvalidUtf8Ignore);

            // [REACT] Bind connection when worker's process requests "hello" action (enables 2-way communication)
            $decoder->on('data', static function (array $data) use ($processPool, $getFileChunk, $decoder, $encoder): void {
                if (ParallelAction::WORKER_HELLO !== $data['action']) {
                    return;
                }

                $identifier = ProcessIdentifier::fromRaw($data['identifier']);
                $process = $processPool->getProcess($identifier);
                $process->bindConnection($decoder, $encoder);
                $fileChunk = $getFileChunk();

                if (0 === \count($fileChunk)) {
                    $process->request(['action' => ParallelAction::RUNNER_THANK_YOU]);
                    $processPool->endProcessIfKnown($identifier);

                    return;
                }

                $process->request(['action' => ParallelAction::RUNNER_REQUEST_ANALYSIS, 'files' => $fileChunk]);
            });
        });

        $processesToSpawn = min(
            $this->parallelConfig->getMaxProcesses(),
            max(
                1,
                (int) ceil($this->fileCount / $this->parallelConfig->getFilesPerProcess()),
            )
        );
        $processFactory = new ProcessFactory($this->input);

        for ($i = 0; $i < $processesToSpawn; ++$i) {
            $identifier = ProcessIdentifier::create();
            $process = $processFactory->create(
                $streamSelectLoop,
                new RunnerConfig(
                    $this->isDryRun,
                    $this->stopOnViolation,
                    $this->parallelConfig,
                    $this->configFile
                ),
                $identifier,
                $serverPort,
            );
            $processPool->addProcess($identifier, $process);
            $process->start(
                // [REACT] Handle workers' responses (multiple actions possible)
                function (array $workerResponse) use ($processPool, $process, $identifier, $getFileChunk, &$changed): void {
                    // File analysis result (we want close-to-realtime progress with frequent cache savings)
                    if (ParallelAction::WORKER_RESULT === $workerResponse['action']) {
                        $fileAbsolutePath = $workerResponse['file'];
                        $fileRelativePath = $this->directory->getRelativePathTo($fileAbsolutePath);

                        // Dispatch an event for each file processed and dispatch its status (required for progress output)
                        $this->dispatchEvent(
                            FixerFileProcessedEvent::NAME,
                            new FixerFileProcessedEvent($workerResponse['status'])
                        );

                        if (isset($workerResponse['fileHash'])) {
                            $this->cacheManager->setFileHash($fileRelativePath, $workerResponse['fileHash']);
                        }

                        foreach ($workerResponse['errors'] ?? [] as $error) {
                            $this->errorsManager->report(new Error(
                                $error['type'],
                                $error['filePath'],
                                null !== $error['source']
                                    ? SourceExceptionFactory::fromArray($error['source'])
                                    : null,
                                $error['appliedFixers'],
                                $error['diff']
                            ));
                        }

                        // Pass-back information about applied changes (only if there are any)
                        if (isset($workerResponse['fixInfo'])) {
                            $changed[$fileRelativePath] = $workerResponse['fixInfo'];

                            if ($this->stopOnViolation) {
                                $processPool->endAll();

                                return;
                            }
                        }

                        return;
                    }

                    if (ParallelAction::WORKER_GET_FILE_CHUNK === $workerResponse['action']) {
                        // Request another chunk of files, if still available
                        $fileChunk = $getFileChunk();

                        if (0 === \count($fileChunk)) {
                            $process->request(['action' => ParallelAction::RUNNER_THANK_YOU]);
                            $processPool->endProcessIfKnown($identifier);

                            return;
                        }

                        $process->request(['action' => ParallelAction::RUNNER_REQUEST_ANALYSIS, 'files' => $fileChunk]);

                        return;
                    }

                    if (ParallelAction::WORKER_ERROR_REPORT === $workerResponse['action']) {
                        throw WorkerException::fromRaw($workerResponse); // @phpstan-ignore-line
                    }

                    throw new ParallelisationException('Unsupported action: '.($workerResponse['action'] ?? 'n/a'));
                },

                // [REACT] Handle errors encountered during worker's execution
                static function (\Throwable $error) use ($processPool): void {
                    $processPool->endAll();

                    throw new ParallelisationException($error->getMessage(), $error->getCode(), $error);
                },

                // [REACT] Handle worker's shutdown
                static function ($exitCode, string $output) use ($processPool, $identifier): void {
                    $processPool->endProcessIfKnown($identifier);

                    if (0 === $exitCode || null === $exitCode) {
                        return;
                    }

                    $errorsReported = Preg::matchAll(
                        sprintf('/^(?:%s)([^\n]+)+/m', WorkerCommand::ERROR_PREFIX),
                        $output,
                        $matches
                    );

                    if ($errorsReported > 0) {
                        throw WorkerException::fromRaw(json_decode($matches[1][0], true));
                    }
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
        $collection = $this->getLintingFileIterator();

        foreach ($collection as $file) {
            $fixInfo = $this->fixFile($file, $collection->currentLintingResult());

            // we do not need Tokens to still caching just fixed file - so clear the cache
            Tokens::clearCache();

            if (null !== $fixInfo) {
                $name = $this->directory->getRelativePathTo($file->__toString());
                $changed[$name] = $fixInfo;

                if ($this->stopOnViolation) {
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

            if (!$this->isDryRun) {
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
            new FixerFileProcessedEvent(null !== $fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES, $name, $newHash)
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

    private function getLintingFileIterator(): LintingResultAwareFileIteratorInterface
    {
        $fileFilterIterator = $this->getFilteringFileIterator();

        return $this->linter->isAsync()
            ? new FileCachingLintingFileIterator($fileFilterIterator, $this->linter)
            : new LintingFileIterator($fileFilterIterator, $this->linter);
    }

    private function getFilteringFileIterator(): FileFilterIterator
    {
        if (null === $this->fileIterator) {
            throw new \RuntimeException('File iterator is not configured. Pass paths during Runner initialisation or set them after with `setFileIterator()`.');
        }

        return new FileFilterIterator(
            $this->fileIterator instanceof \IteratorAggregate
                ? $this->fileIterator->getIterator()
                : $this->fileIterator,
            $this->eventDispatcher,
            $this->cacheManager
        );
    }
}
