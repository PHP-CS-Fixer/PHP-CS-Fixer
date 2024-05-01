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

namespace PhpCsFixer\Console\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Runner\Parallel\ParallelAction;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\ReadonlyCacheManager;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ToolInfoInterface;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
#[AsCommand(name: 'worker', description: 'Internal command for running fixers in parallel', hidden: true)]
final class WorkerCommand extends Command
{
    /** @var string Prefix used before JSON-encoded error printed in the worker's process */
    public const ERROR_PREFIX = 'WORKER_ERROR::';

    /** @var string */
    protected static $defaultName = 'worker';

    /** @var string */
    protected static $defaultDescription = 'Internal command for running fixers in parallel';

    private ToolInfoInterface $toolInfo;
    private ConfigurationResolver $configurationResolver;
    private ErrorsManager $errorsManager;
    private EventDispatcherInterface $eventDispatcher;

    /** @var list<FixerFileProcessedEvent> */
    private array $events;

    public function __construct(ToolInfoInterface $toolInfo)
    {
        parent::__construct();

        $this->setHidden(true);
        $this->toolInfo = $toolInfo;
        $this->errorsManager = new ErrorsManager();
        $this->eventDispatcher = new EventDispatcher();
    }

    protected function configure(): void
    {
        $this->setDefinition(
            [
                new InputOption(
                    'port',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Specifies parallelisation server\'s port.'
                ),
                new InputOption(
                    'identifier',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Specifies parallelisation process\' identifier.'
                ),
                new InputOption(
                    'allow-risky',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'Are risky fixers allowed (can be `yes` or `no`).'
                ),
                new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a config file.'),
                new InputOption(
                    'dry-run',
                    '',
                    InputOption::VALUE_NONE,
                    'Only shows which files would have been modified.'
                ),
                new InputOption(
                    'rules',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'List of rules that should be run against configured paths.'
                ),
                new InputOption(
                    'using-cache',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'Should cache be used (can be `yes` or `no`).'
                ),
                new InputOption('cache-file', '', InputOption::VALUE_REQUIRED, 'The path to the cache file.'),
                new InputOption('diff', '', InputOption::VALUE_NONE, 'Prints diff for each file.'),
            ]
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $identifier = $input->getOption('identifier');
        $port = $input->getOption('port');

        if (null === $identifier || !is_numeric($port)) {
            throw new ParallelisationException('Missing parallelisation options');
        }

        try {
            $runner = $this->createRunner($input);
        } catch (\Throwable $e) {
            throw new ParallelisationException('Unable to create runner: '.$e->getMessage(), 0, $e);
        }

        $loop = new StreamSelectLoop();
        $tcpConnector = new TcpConnector($loop);
        $tcpConnector
            ->connect(sprintf('127.0.0.1:%d', $port))
            ->then(
                /** @codeCoverageIgnore */
                function (ConnectionInterface $connection) use ($loop, $runner, $identifier): void {
                    $jsonInvalidUtf8Ignore = \defined('JSON_INVALID_UTF8_IGNORE') ? JSON_INVALID_UTF8_IGNORE : 0;
                    $out = new Encoder($connection, $jsonInvalidUtf8Ignore);
                    $in = new Decoder($connection, true, 512, $jsonInvalidUtf8Ignore);

                    // [REACT] Initialise connection with the parallelisation operator
                    $out->write(['action' => ParallelAction::RUNNER_HELLO, 'identifier' => $identifier]);

                    $handleError = static function (\Throwable $error) use ($out): void {
                        $out->write([
                            'action' => ParallelAction::RUNNER_ERROR_REPORT,
                            'message' => $error->getMessage(),
                            'file' => $error->getFile(),
                            'line' => $error->getLine(),
                            'code' => $error->getCode(),
                            'trace' => $error->getTraceAsString(),
                        ]);
                    };
                    $out->on('error', $handleError);
                    $in->on('error', $handleError);

                    // [REACT] Listen for messages from the parallelisation operator (analysis requests)
                    $in->on('data', function (array $json) use ($loop, $runner, $out): void {
                        $action = $json['action'] ?? null;

                        // Parallelisation operator does not have more to do, let's close the connection
                        if (ParallelAction::WORKER_THANK_YOU === $action) {
                            $loop->stop();

                            return;
                        }

                        // At this point we only expect analysis requests, so let's return early for any other message
                        if (ParallelAction::WORKER_RUN !== $action) {
                            return;
                        }

                        /** @var iterable<int, string> $files */
                        $files = $json['files'];

                        foreach ($files as $i => $absolutePath) {
                            $relativePath = $this->configurationResolver->getDirectory()->getRelativePathTo($absolutePath);

                            // Reset events because we want to collect only those coming from analysed files chunk
                            $this->events = [];
                            $runner->setFileIterator(new \ArrayIterator([new \SplFileInfo($absolutePath)]));
                            $analysisResult = $runner->fix();

                            $out->write([
                                'action' => ParallelAction::RUNNER_RESULT,
                                'file' => $absolutePath,
                                // @phpstan-ignore-next-line False-positive caused by assigning empty array to $events property
                                'status' => isset($this->events[0]) ? $this->events[0]->getStatus() : null,
                                'fixInfo' => $analysisResult[$relativePath] ?? null,
                                'errors' => $this->errorsManager->forPath($absolutePath),
                            ]);
                        }

                        // Request another file chunk (if available, the parallelisation operator will request new "run" action)
                        $out->write(['action' => ParallelAction::RUNNER_GET_FILE_CHUNK]);
                    });
                },
                static function (\Throwable $error) use ($errorOutput): void {
                    $errorOutput->writeln($error->getMessage());
                }
            )
        ;

        $loop->run();

        return Command::SUCCESS;
    }

    private function createRunner(InputInterface $input): Runner
    {
        $passedConfig = $input->getOption('config');
        $passedRules = $input->getOption('rules');

        if (null !== $passedConfig && null !== $passedRules) {
            throw new \RuntimeException('Passing both `--config` and `--rules` options is not allowed');
        }

        // There's no one single source of truth when it comes to fixing single file, we need to collect statuses from events.
        $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, function (FixerFileProcessedEvent $event): void {
            $this->events[] = $event;
        });

        $this->configurationResolver = new ConfigurationResolver(
            new Config(),
            [
                'allow-risky' => $input->getOption('allow-risky'),
                'config' => $passedConfig,
                'dry-run' => $input->getOption('dry-run'),
                'rules' => $passedRules,
                'path' => [],
                'path-mode' => ConfigurationResolver::PATH_MODE_OVERRIDE,
                'using-cache' => $input->getOption('using-cache'),
                'cache-file' => $input->getOption('cache-file'),
                'diff' => $input->getOption('diff'),
                'stop-on-violation' => false, // @TODO Pass this option to the runner
            ],
            getcwd(), // @phpstan-ignore-line
            $this->toolInfo
        );

        return new Runner(
            null, // Paths are known when parallelisation server requests new chunk, not now
            $this->configurationResolver->getFixers(),
            $this->configurationResolver->getDiffer(),
            $this->eventDispatcher,
            $this->errorsManager,
            $this->configurationResolver->getLinter(),
            $this->configurationResolver->isDryRun(),
            new ReadonlyCacheManager($this->configurationResolver->getCacheManager()),
            $this->configurationResolver->getDirectory(),
            $this->configurationResolver->shouldStopOnViolation(),
            ParallelConfig::sequential(), // IMPORTANT! Worker must run in sequential mode
            null,
            $this->configurationResolver->getConfigFile()
        );
    }
}
