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

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\FixCommand
 */
final class FixCommandTest extends TestCase
{
    public function testIntersectionPathMode(): void
    {
        $cmdTester = $this->doTestExecute([
            '--path-mode' => 'intersection',
            '--show-progress' => 'none',
        ]);

        static::assertSame(
            Command::SUCCESS,
            $cmdTester->getStatusCode()
        );
    }

    public function testEmptyRulesValue(): void
    {
        $this->expectException(
            InvalidConfigurationException::class
        );
        $this->expectExceptionMessageMatches(
            '#^Empty rules value is not allowed\.$#'
        );

        $this->doTestExecute(
            ['--rules' => '']
        );
    }

    public function testEmptyFormatValue(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Expected "yes" or "no" for option "using-cache", got "not today".');

        $cmdTester = $this->doTestExecute(
            [
                '--using-cache' => 'not today',
                '--rules' => 'switch_case_semicolon_to_colon',
            ]
        );

        $cmdTester->getStatusCode();
    }

    /**
     * @param array<string, bool|string> $arguments
     */
    private function doTestExecute(array $arguments): CommandTester
    {
        $application = new Application();
        $application->add(new FixCommand(new ToolInfo()));

        $command = $application->find('fix');
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

    /**
     * @return array<string, mixed>
     */
    private function getDefaultArguments(): array
    {
        return [
            'path' => [__FILE__],
            '--path-mode' => 'override',
            '--allow-risky' => 'yes',
            '--dry-run' => true,
            '--using-cache' => 'no',
            '--show-progress' => 'none',
        ];
    }
}
