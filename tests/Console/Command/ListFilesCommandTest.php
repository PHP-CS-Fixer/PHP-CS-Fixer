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
use PhpCsFixer\Console\Command\ListFilesCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ListFilesCommand
 */
final class ListFilesCommandTest extends TestCase
{
    public function testEmptyRulesValue()
    {
        $commandTester = $this->doTestExecute();
        // TODO adjust expectation
        $this->assertSame('', $commandTester->getDisplay());
    }

    /**
     * @return CommandTester
     */
    private function doTestExecute(/*array $arguments*/)
    {
        $application = new Application();
        $application->add(new ListFilesCommand(new ToolInfo()));

        $command = $application->find('list-files');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--config' => __DIR__.'/test-project/.php_cs'
        ]
        );

        return $commandTester;
    }
}
