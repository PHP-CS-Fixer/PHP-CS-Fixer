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

namespace PhpCsFixer\Tests\Runner\Parallel;

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\Process;
use PhpCsFixer\Runner\Parallel\ProcessFactory;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Runner\Parallel\ProcessPool;
use PhpCsFixer\Runner\RunnerConfig;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ServerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ProcessPool
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ProcessPoolTest extends TestCase
{
    public bool $serverClosed = false;

    private ArrayInput $arrayInput;
    private ProcessFactory $processFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $fixCommand = new FixCommand(new ToolInfo());
        $application = new Application();
        $application->addCommands([$fixCommand]);

        // In order to have full list of options supported by the command (e.g. `--verbose`)
        $fixCommand->mergeApplicationDefinition(false); // @phpstan-ignore method.internal

        $this->arrayInput = new ArrayInput([], $fixCommand->getDefinition());

        $this->processFactory = new ProcessFactory();
    }

    public function testGetProcessWithInvalidIdentifier(): void
    {
        self::expectException(ParallelisationException::class);

        $this->getProcessPool()->getProcess(ProcessIdentifier::create());
    }

    public function testGetProcessWithValidIdentifier(): void
    {
        $identifier = ProcessIdentifier::create();
        $processPool = $this->getProcessPool();
        $process = $this->createProcess($identifier);

        $processPool->addProcess($identifier, $process);

        self::assertSame($process, $processPool->getProcess($identifier));
    }

    public function testEndProcessIfKnownWithUnknownIdentifier(): void
    {
        $identifier1 = ProcessIdentifier::create();
        $identifier2 = ProcessIdentifier::create();
        $processPool = $this->getProcessPool();
        $process = $this->createProcess($identifier1);

        $processPool->addProcess($identifier1, $process);

        // This is unregistered process, so it does nothing
        $processPool->endProcessIfKnown($identifier2);

        self::assertFalse($this->serverClosed);
    }

    public function testEndProcessIfKnownWithKnownIdentifier(): void
    {
        $identifier = ProcessIdentifier::create();
        $processPool = $this->getProcessPool();
        $process = $this->createProcess($identifier);
        $processPool->addProcess($identifier, $process);
        $processPool->endProcessIfKnown($identifier);

        self::assertTrue($this->serverClosed);
    }

    public function testEndAll(): void
    {
        $callbackExecuted = false;
        $processPool = $this->getProcessPool(static function () use (&$callbackExecuted): void {
            $callbackExecuted = true;
        });

        $identifier1 = ProcessIdentifier::create();
        $process1 = $this->createProcess($identifier1);
        $processPool->addProcess($identifier1, $process1);

        $identifier2 = ProcessIdentifier::create();
        $process2 = $this->createProcess($identifier2);
        $processPool->addProcess($identifier2, $process2);

        $processPool->endAll();

        self::assertTrue($this->serverClosed);
        self::assertTrue($callbackExecuted, 'Callback was not executed on server close.');
    }

    private function createProcess(ProcessIdentifier $identifier): Process
    {
        return $this->processFactory->create(
            new StreamSelectLoop(),
            $this->arrayInput,
            new RunnerConfig(
                true,
                false,
                ParallelConfigFactory::sequential(),
            ),
            $identifier,
            10_000,
        );
    }

    private function getProcessPool(?callable $onServerClose = null): ProcessPool
    {
        $this->serverClosed = false;
        $test = $this;

        return new ProcessPool(
            new class($test) implements ServerInterface {
                private ProcessPoolTest $test;

                public function __construct(ProcessPoolTest $test)
                {
                    $this->test = $test;
                }

                public function close(): void
                {
                    $this->test->serverClosed = true;
                }

                public function getAddress(): ?string
                {
                    return null;
                }

                public function pause(): void {}

                public function resume(): void {}

                /** @phpstan-ignore-next-line */
                public function on($event, $listener): void {}

                /** @phpstan-ignore-next-line */
                public function once($event, $listener): void {}

                /** @phpstan-ignore-next-line */
                public function removeListener($event, $listener): void {}

                /** @phpstan-ignore-next-line */
                public function removeAllListeners($event = null): void {}

                /** @phpstan-ignore-next-line */
                public function listeners($event = null): void {}

                /** @phpstan-ignore-next-line */
                public function emit($event, array $arguments = []): void {}
            },
            $onServerClose,
        );
    }
}
