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

        self::assertSame(
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
     * There's no simple way to cover parallelisation with tests, because it involves a lot of hardcoded logic under the hood,
     * like opening server, communicating through sockets, etc. That's why we only test `fix` command with proper
     * parallel config, so runner utilises multi-processing internally. Expected outcome is.
     *
     * @covers \PhpCsFixer\Console\Command\WorkerCommand
     * @covers \PhpCsFixer\Runner\Runner::fixParallel
     */
    public function testParallelRun(): void
    {
        $pathToDistConfig = __DIR__.'/../../../.php-cs-fixer.dist.php';
        $configWithFixedParallelConfig = <<<PHP
            <?php

            \$config = require '{$pathToDistConfig}';
            \$config->setRules(['header_comment' => ['header' => 'PARALLEL!']]);
            \$config->setParallelConfig(new \\PhpCsFixer\\Runner\\Parallel\\ParallelConfig(2, 1, 300));

            return \$config;
            PHP;
        $tmpFile = tempnam(sys_get_temp_dir(), 'php-cs-fixer-parallel-config-').'.php';
        file_put_contents($tmpFile, $configWithFixedParallelConfig);

        $cmdTester = $this->doTestExecute(
            [
                '--config' => $tmpFile,
                'path' => [__DIR__],
            ]
        );

        self::assertStringContainsString('Running analysis on 2 cores with 1 file per process.', $cmdTester->getDisplay());
        self::assertStringContainsString('(header_comment)', $cmdTester->getDisplay());
        self::assertSame(8, $cmdTester->getStatusCode());
    }

    /**
     * @param array<string, mixed> $arguments
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
