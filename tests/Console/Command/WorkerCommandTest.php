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

namespace PhpCsFixer\Tests\Console\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Command\WorkerCommand;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Runner\Parallel\ParallelAction;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\ProcessFactory;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Runner\RunnerConfig;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use React\ChildProcess\Process;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\WorkerCommand
 */
final class WorkerCommandTest extends TestCase
{
    public function testMissingIdentifierCausesFailure(): void
    {
        self::expectException(ParallelisationException::class);
        self::expectExceptionMessage('Missing parallelisation options');

        $commandTester = $this->doTestExecute(['--port' => 12_345]);
    }

    public function testMissingPortCausesFailure(): void
    {
        self::expectException(ParallelisationException::class);
        self::expectExceptionMessage('Missing parallelisation options');

        $commandTester = $this->doTestExecute(['--identifier' => (string) ProcessIdentifier::create()]);
    }

    public function testWorkerCantConnectToServerWhenExecutedDirectly(): void
    {
        $commandTester = $this->doTestExecute([
            '--identifier' => (string) ProcessIdentifier::create(),
            '--port' => 12_345,
        ]);

        self::assertStringContainsString(
            'Connection refused',
            $commandTester->getErrorOutput()
        );
    }

    /**
     * @requires OS Linux|Darwin
     */
    public function testWorkerCommunicatesWithTheServer(): void
    {
        $streamSelectLoop = new StreamSelectLoop();
        $server = new TcpServer('127.0.0.1:0', $streamSelectLoop);
        $serverPort = parse_url($server->getAddress() ?? '', PHP_URL_PORT);
        $processIdentifier = ProcessIdentifier::create();
        $processFactory = new ProcessFactory(
            new ArrayInput([], (new FixCommand(new ToolInfo()))->getDefinition())
        );
        $process = new Process(implode(' ', $processFactory->getCommandArgs(
            $serverPort, // @phpstan-ignore-line
            $processIdentifier,
            new RunnerConfig(true, false, ParallelConfig::sequential())
        )));

        /**
         * @var array{
         *     identifier: string,
         *     messages: list<array<string, mixed>>,
         *     connected: bool,
         *     chunkRequested: bool,
         *     resultReported: bool
         * } $workerScope
         */
        $workerScope = [
            'identifier' => (string) $processIdentifier,
            'messages' => [],
            'connected' => false,
            'chunkRequested' => false,
            'resultReported' => false,
        ];

        $server->on(
            'connection',
            static function (ConnectionInterface $connection) use (&$workerScope): void {
                $jsonInvalidUtf8Ignore = \defined('JSON_INVALID_UTF8_IGNORE') ? JSON_INVALID_UTF8_IGNORE : 0;
                $decoder = new Decoder($connection, true, 512, $jsonInvalidUtf8Ignore);
                $encoder = new Encoder($connection, $jsonInvalidUtf8Ignore);

                $decoder->on(
                    'data',
                    static function (array $data) use ($encoder, &$workerScope): void {
                        $workerScope['messages'][] = $data;
                        $ds = \DIRECTORY_SEPARATOR;

                        if (ParallelAction::RUNNER_HELLO === $data['action']) {
                            $encoder->write(['action' => ParallelAction::WORKER_RUN, 'files' => [
                                realpath(__DIR__.$ds.'..'.$ds.'..').$ds.'Fixtures'.$ds.'FixerTest'.$ds.'fix'.$ds.'somefile.php',
                            ]]);

                            return;
                        }

                        if (3 === \count($workerScope['messages'])) {
                            $encoder->write(['action' => ParallelAction::WORKER_THANK_YOU]);
                        }
                    }
                );
            }
        );
        $process->on('exit', static function () use ($streamSelectLoop): void {
            $streamSelectLoop->stop();
        });

        // Start worker in the async process, handle communication with server and wait for it to exit
        $process->start($streamSelectLoop);
        $streamSelectLoop->run();

        self::assertSame(Command::SUCCESS, $process->getExitCode());
        self::assertCount(3, $workerScope['messages']);
        self::assertSame(ParallelAction::RUNNER_HELLO, $workerScope['messages'][0]['action']);
        self::assertSame(ParallelAction::RUNNER_RESULT, $workerScope['messages'][1]['action']);
        self::assertSame(FixerFileProcessedEvent::STATUS_FIXED, $workerScope['messages'][1]['status']);
        self::assertSame(ParallelAction::RUNNER_GET_FILE_CHUNK, $workerScope['messages'][2]['action']);

        $server->close();
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function doTestExecute(array $arguments): CommandTester
    {
        $application = new Application();
        $application->add(new WorkerCommand(new ToolInfo()));

        $command = $application->find('worker');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array_merge(
                ['command' => $command->getName()],
                $arguments
            ),
            [
                'capture_stderr_separately' => true,
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ]
        );

        return $commandTester;
    }
}
