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
use PhpCsFixer\Console\Command\CheckCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\CheckCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CheckCommandTest extends TestCase
{
    /**
     * This test ensures that `--dry-run` option is not available in `check` command,
     * because this command is a proxy for `fix` command which always set `--dry-run` during proxying,
     * so it does not make sense to provide this option again.
     */
    public function testDryRunModeIsUnavailable(): void
    {
        $application = new Application();
        $application->add(new CheckCommand(new ToolInfo()));

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessageMatches('/--dry-run/');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--dry-run' => true,
            ],
        );
    }

    public function testOutputWithCacheFileInSubdirectory(): void
    {
        $filesystem = new Filesystem();
        $tmpDir = sys_get_temp_dir().'/php-cs-fixer-test-'.bin2hex(random_bytes(8));
        $filesystem->mkdir($tmpDir);
        $filesystem->mkdir($tmpDir.'/var');

        $originalCwd = (string) getcwd();

        try {
            chdir($tmpDir);

            file_put_contents($tmpDir.'/test.php', "<?php\n\n\$a = [1, 2,];\n");

            $application = new Application();
            $application->add(new CheckCommand(new ToolInfo()));

            $command = $application->find('check');
            $commandTester = new CommandTester($command);

            $commandTester->execute(
                [
                    'command' => $command->getName(),
                    'path' => ['test.php'],
                    '--cache-file' => 'var/.php-cs-fixer.cache',
                    '--rules' => 'no_trailing_comma_in_singleline',
                    '--diff' => true,
                    '--show-progress' => 'none',
                ],
                [
                    'interactive' => false,
                    'decorated' => false,
                    'verbosity' => OutputInterface::VERBOSITY_DEBUG,
                ],
            );

            $output = $commandTester->getDisplay();

            self::assertStringContainsString('1) test.php', $output);
            self::assertStringNotContainsString('1) '.$tmpDir, $output);
        } finally {
            chdir($originalCwd);
            $filesystem->remove($tmpDir);
        }
    }
}
