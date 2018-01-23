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
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\FixCommand
 */
final class FixCommandTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        parent::setUp();

        $this->application = new Application();
    }

    public function testEmptyRulesValue()
    {
        $this->expectException(
            'PhpCsFixer\ConfigurationException\InvalidConfigurationException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Empty rules value is not allowed\.$#'
        );

        $this->doTestExecute(
            ['--rules' => '']
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation Expected "yes" or "no" for option "using-cache", other values are deprecated and support will be removed in 3.0. Got "not today", this implicitly set the option to "false".
     */
    public function testEmptyFormatValue()
    {
        $cmdTester = $this->doTestExecute(
            [
                '--using-cache' => 'not today',
                '--rules' => 'switch_case_semicolon_to_colon',
            ]
        );

        $this->assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
    }

    /**
     * @param array $arguments
     *
     * @return CommandTester
     */
    private function doTestExecute(array $arguments)
    {
        $this->application->add(new FixCommand(new ToolInfo()));

        $command = $this->application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array_merge(
                ['command' => $command->getName()],
                $this->getDefaultArguments(),
                $arguments
            ),
            [
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ]
        );

        return $commandTester;
    }

    private function getDefaultArguments()
    {
        return [
            'path' => [__FILE__],
            '--path-mode' => 'override',
            '--allow-risky' => true,
            '--dry-run' => true,
            '--using-cache' => 'no',
            '--show-progress' => 'none',
        ];
    }
}
