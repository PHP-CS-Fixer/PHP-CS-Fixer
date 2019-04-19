<?php

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
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    public function testShowCommand()
    {
        $cmdTester = $this->doTestExecute();

        $this->assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
    }

    /**
     * @return CommandTester
     */
    private function doTestExecute()
    {
        $this->application->add(new ListFixersCommand(new ToolInfo()));

        $command = $this->application->find('list');
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
}
