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

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\WorkerCommand;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Command\Command;
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
        $commandTester = $this->doTestExecute(['--port' => 12_345]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertStringContainsString('Missing parallelisation options', $commandTester->getErrorOutput());
    }

    public function testMissingCausesFailure(): void
    {
        $commandTester = $this->doTestExecute(['--identifier' => (string) ProcessIdentifier::create()]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertStringContainsString('Missing parallelisation options', $commandTester->getErrorOutput());
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
