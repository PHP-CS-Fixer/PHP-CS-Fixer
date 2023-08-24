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
use PhpCsFixer\Console\Command\ListFixersCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ListFixersCommand
 */
final class ListFixersCommandTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();
    }

    public function testShowCommand(): void
    {
        $cmdTester = $this->doTestExecute();

        $expected = $cmdTester->getDisplay();
        $this->saveExpected('no_options', $expected);

        self::assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
    }

    private function doTestExecute(): CommandTester
    {
        $this->application->add(new ListFixersCommand(new ToolInfo()));

        $command = $this->application->find(ListFixersCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                $command->getName(),
            ],
            [
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ]
        );

        return $commandTester;
    }

    /**
     * @deprecated Just useful while writing those tests
     */
    private function saveExpected(string $filename, string $content):void
    {
        file_put_contents(sprintf(__DIR__.'/expected/%s.txt', $filename), $content);
    }
}
