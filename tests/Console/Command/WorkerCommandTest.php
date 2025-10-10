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
use PhpCsFixer\Runner\Event\FileProcessed;
use PhpCsFixer\Runner\Parallel\ParallelAction;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class WorkerCommandTest extends TestCase
{
    public function testMissingIdentifierCausesFailure(): void
    {
        self::expectException(ParallelisationException::class);
        self::expectExceptionMessage('Missing parallelisation options');

        $this->doTestExecute(['--port' => 12_345]);
    }

    public function testMissingPortCausesFailure(): void
    {
        self::expectException(ParallelisationException::class);
        self::expectExceptionMessage('Missing parallelisation options');

        $this->doTestExecute(['--identifier' => ProcessIdentifier::create()->toString()]);
    }

    public function testWorkerCantConnectToServerWhenExecutedDirectly(): void
    {
        $commandTester = $this->doTestExecute([
            '--identifier' => ProcessIdentifier::create()->toString(),
            '--port' => 12_345,
        ]);

        self::assertStringContainsString(
            'Connection refused',
            $commandTester->getErrorOutput()
        );
    }

    /**
     * This test is not executed on Windows because process pipes are not supported there, due to their blocking nature
     * on this particular OS. The cause of this lays in `react/child-process` component, but it's related only to tests,
     * as parallel runner works properly on Windows too. Feel free to fiddle with it and add testing support for Windows.
     *
     * @requires OS Linux|Darwin
     */
    public function testWorkerCommunicatesWithTheServer(): void
    {
        $streamSelectLoop = new StreamSelectLoop();
        $server = new TcpServer('127.0.0.1:0', $streamSelectLoop);
        $serverPort = parse_url($server->getAddress() ?? '', \PHP_URL_PORT);
        $processIdentifier = ProcessIdentifier::create();
        $processFactory = new ProcessFactory();
        $process = new Process(implode(' ', $processFactory->getCommandArgs(
            $serverPort, // @phpstan-ignore-line
            $processIdentifier,
            new ArrayInput([], (new FixCommand(new ToolInfo()))->getDefinition()),
            new RunnerConfig(true, false, ParallelConfigFactory::sequential())
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
            'identifier' => $processIdentifier->toString(),
            'messages' => [],
            'connected' => false,
            'chunkRequested' => false,
            'resultReported' => false,
        ];

        $server->on(
            'connection',
            static function (ConnectionInterface $connection) use (&$workerScope): void {
                $decoder = new Decoder($connection, true, 512, \JSON_INVALID_UTF8_IGNORE);
                $encoder = new Encoder($connection, \JSON_INVALID_UTF8_IGNORE);

                $decoder->on(
                    'data',
                    static function (array $data) use ($encoder, &$workerScope): void {
                        $workerScope['messages'][] = $data;
                        $ds = \DIRECTORY_SEPARATOR;

                        \assert(\array_key_exists('action', $data));
                        if (ParallelAction::WORKER_HELLO === $data['action']) {
                            $encoder->write(['action' => ParallelAction::RUNNER_REQUEST_ANALYSIS, 'files' => [
                                realpath(__DIR__.$ds.'..'.$ds.'..').$ds.'Fixtures'.$ds.'FixerTest'.$ds.'fix'.$ds.'somefile.php',
                            ]]);

                            return;
                        }

                        if (3 === \count($workerScope['messages'])) {
                            $encoder->write(['action' => ParallelAction::RUNNER_THANK_YOU]);
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
        self::assertArrayHasKey('action', $workerScope['messages'][0]);
        self::assertSame(ParallelAction::WORKER_HELLO, $workerScope['messages'][0]['action']);
        self::assertArrayHasKey('action', $workerScope['messages'][1]);
        self::assertSame(ParallelAction::WORKER_RESULT, $workerScope['messages'][1]['action']);
        self::assertArrayHasKey('status', $workerScope['messages'][1]);
        self::assertSame(FileProcessed::STATUS_FIXED, $workerScope['messages'][1]['status']);
        self::assertArrayHasKey('action', $workerScope['messages'][2]);
        self::assertSame(ParallelAction::WORKER_GET_FILE_CHUNK, $workerScope['messages'][2]['action']);

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
