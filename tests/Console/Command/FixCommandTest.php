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

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
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
     * @covers \PhpCsFixer\Console\Command\WorkerCommand
     * @covers \PhpCsFixer\Runner\Runner::fixSequential
     */
    public function testSequentialRun(): void
    {
        $pathToDistConfig = __DIR__.'/../../../.php-cs-fixer.dist.php';
        $configWithFixedParallelConfig = <<<PHP
            <?php

            \$config = require '{$pathToDistConfig}';
            \$config->setRules(['header_comment' => ['header' => 'SEQUENTIAL!']]);
            \$config->setParallelConfig(\\PhpCsFixer\\Runner\\Parallel\\ParallelConfigFactory::sequential());

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

        $availableMaxProcesses = ParallelConfigFactory::detect()->getMaxProcesses();

        self::assertStringContainsString('Running analysis on 1 core sequentially.', $cmdTester->getDisplay());
        if ($availableMaxProcesses > 1) {
            self::assertStringContainsString('You can enable parallel runner and speed up the analysis!', $cmdTester->getDisplay());
        }
        self::assertStringContainsString('(header_comment)', $cmdTester->getDisplay());
        self::assertSame(8, $cmdTester->getStatusCode());
    }

    /**
     * There's no simple way to cover parallelisation with tests, because it involves a lot of hardcoded logic under the hood,
     * like opening server, communicating through sockets, etc. That's why we only test `fix` command with proper
     * parallel config, so runner utilises multi-processing internally. Expected outcome is information about utilising multiple CPUs.
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
        self::assertStringContainsString('Parallel runner is an experimental feature and may be unstable, use it at your own risk. Feedback highly appreciated!', $cmdTester->getDisplay());
        self::assertStringContainsString('(header_comment)', $cmdTester->getDisplay());
        self::assertSame(8, $cmdTester->getStatusCode());
    }

    /**
     * @large
     */
    public function testUnsupportedVersionWarningRun(): void
    {
        if (version_compare(\PHP_VERSION, ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED.'.99', '<=')) {
            self::markTestSkipped('This test requires version of PHP higher than '.ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED);
        }

        $pathToDistConfig = __DIR__.'/../../../.php-cs-fixer.dist.php';
        $configWithFixedParallelConfig = <<<PHP
            <?php

            \$config = require '{$pathToDistConfig}';
            \$config->setUnsupportedPhpVersionAllowed(true);

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

        self::assertStringContainsString('PHP CS Fixer currently supports PHP syntax only up to PHP '.ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED, $cmdTester->getDisplay());
        self::assertStringContainsString('Execution may be unstable. You may experience code modified in a wrong way.', $cmdTester->getDisplay());
    }

    public function testUnsupportedVersionErrorRun(): void
    {
        if (version_compare(\PHP_VERSION, ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED.'.99', '<=')) {
            self::markTestSkipped('This test requires version of PHP higher than '.ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED);
        }

        $pathToDistConfig = __DIR__.'/../../../.php-cs-fixer.dist.php';
        $configWithFixedParallelConfig = <<<PHP
            <?php

            \$config = require '{$pathToDistConfig}';
            \$config->setUnsupportedPhpVersionAllowed(false);

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

        self::assertStringContainsString('PHP CS Fixer currently supports PHP syntax only up to PHP '.ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED, $cmdTester->getDisplay());
        self::assertStringContainsString('Add Config::setUnsupportedPhpVersionAllowed(true) to allow executions on unsupported PHP versions.', $cmdTester->getDisplay());
        self::assertSame(1, $cmdTester->getStatusCode());
    }

    public function testLoadedConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Using config from passed path is deprecated and will be removed in version 4.0, please use "--config" instead, or move the config to the current working directory.');

        $this->doTestExecute(
            [
                'path' => [__DIR__.'/../../Fixtures/ci-integration'],
            ]
        );
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
            'path' => [__DIR__.'/../../Fixtures/dummy-file.php'],
            '--path-mode' => 'override',
            '--allow-risky' => 'yes',
            '--dry-run' => true,
            '--using-cache' => 'no',
            '--show-progress' => 'none',
        ];
    }
}
