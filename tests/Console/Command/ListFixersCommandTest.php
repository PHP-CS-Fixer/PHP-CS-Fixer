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

    public function testConfigOptionNotPassedUsesDefaultConfigFile(): void
    {
        $cmdTester = $this->doTestExecute();

        $result = $cmdTester->getDisplay();

        self::assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());

        // "  // Loaded config default from /path/to/library/.php-cs-fixer.dist.php."
        self::assertStringContainsString('// Loaded config default from', $result);
        self::assertStringContainsString('/.php-cs-fixer.dist.php.', $result);
    }

    public function testConfigOptionThrowsExceptionIfCustomConfigFileDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot read config file "/not/existent/config_file.php".');

        $cmdTester = $this->doTestExecute([ListFixersCommand::OPT_CONFIG => '/not/existent/config_file.php']);

        self::assertNotSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
    }

    public function testOptionsHideConfiguredAndOnlyConfiguredCannotBeUsedTogether(): void
    {
        $this->expectException(\LogicException::class);

        $cmdTester = $this->doTestExecute([
            ListFixersCommand::OPT_HIDE_CONFIGURED => true,
            ListFixersCommand::OPT_ONLY_CONFIGURED => true,
        ]);

        self::assertNotSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
    }

    public function testOptionOnlyConfiguredShowsOnlyExplicitlyConfiguredFixers(): void
    {
        $fileName = ListFixersCommand::OPT_ONLY_CONFIGURED;
        $configFile = $this->getConfigFilePath($fileName);
        $cmdTester = $this->doTestExecute([
            ListFixersCommand::OPT_CONFIG => $configFile,
            ListFixersCommand::OPT_ONLY_CONFIGURED => true,
        ]);

        $result = $cmdTester->getDisplay();
        $result = $this->prepareOutputForTest($result);

        // $this->saveExpected($fileName, $result);

        $expected = $this->loadExpectedOutput($fileName);

        self::assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
        self::assertSame($expected, $result);
    }

    public function testOptionHideInheritanceActuallyHidesSetsThatEnableAFixer(): void
    {
        $fileName = ListFixersCommand::OPT_HIDE_INHERITANCE;
        $configFile = $this->getConfigFilePath($fileName);
        $cmdTester = $this->doTestExecute([
            ListFixersCommand::OPT_CONFIG => $configFile,
            ListFixersCommand::OPT_ONLY_CONFIGURED => true,
            ListFixersCommand::OPT_HIDE_INHERITANCE => true,
        ]);

        $result = $cmdTester->getDisplay();
        $result = $this->prepareOutputForTest($result);

        // $this->saveExpected($fileName, $result);

        $expected = $this->loadExpectedOutput($fileName);

        self::assertSame(0, $cmdTester->getStatusCode(), "Expected exit code mismatch. Output:\n".$cmdTester->getDisplay());
        self::assertSame($expected, $result);
    }

    private function doTestExecute(array $options = []): CommandTester
    {
        $this->application->add(new ListFixersCommand(new ToolInfo()));

        $command = $this->application->find(ListFixersCommand::NAME);
        $commandTester = new CommandTester($command);

        $commandOptions = [$command->getName()];

        foreach ($options as $option => $value) {
            $commandOptions[sprintf('--%s', $option)] = $value;
        }

        $commandTester->execute(
            $commandOptions,
            [
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ],
        );

        return $commandTester;
    }

    /**
     * @deprecated Just useful while writing those tests
     */
    private function saveExpected(string $filename, string $content):void
    {
        file_put_contents(sprintf(__DIR__.'/../../Fixtures/ListFixersCommand/%s.expected.txt', $filename), $content);
    }

    private function prepareOutputForTest(string $output): string
    {
        $exploded = explode("\n", $output);

        unset($exploded[0], $exploded[1], $exploded[2], $exploded[3]);

        return implode("\n", $exploded);
    }

    private function getConfigFilePath(string $filename): string
    {
        return sprintf(__DIR__.'/../../Fixtures/ListFixersCommand/%s.config.php', $filename);
    }

    private function loadExpectedOutput(string $filename): string
    {
        return file_get_contents(sprintf(__DIR__.'/../../Fixtures/ListFixersCommand/%s.expected.txt', $filename));
    }
}
