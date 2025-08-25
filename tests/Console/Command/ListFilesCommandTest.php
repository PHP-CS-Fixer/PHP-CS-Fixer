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
use PhpCsFixer\Console\Command\ListFilesCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ListFilesCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ListFilesCommandTest extends TestCase
{
    private static ?Filesystem $filesystem;

    public static function setUpBeforeClass(): void
    {
        self::$filesystem = new Filesystem();
    }

    public static function tearDownAfterClass(): void
    {
        self::$filesystem = null;
    }

    public function testListWithConfig(): void
    {
        $commandTester = $this->doTestExecute([
            '--config' => __DIR__.'/../../Fixtures/ListFilesTest/.php-cs-fixer.php',
        ]);

        $expectedPath = './tests/Fixtures/ListFilesTest/needs-fixing/needs-fixing.php';
        // make the test also work on Windows
        $expectedPath = str_replace('/', \DIRECTORY_SEPARATOR, $expectedPath);

        self::assertSame(0, $commandTester->getStatusCode());
        self::assertSame(escapeshellarg($expectedPath).\PHP_EOL, $commandTester->getDisplay());
    }

    /**
     * @requires OS Linux|Darwin
     *
     * Skip test on Windows as `getcwd()` includes the drive letter with a colon `:` which is illegal in filenames.
     */
    public function testListFilesDoesNotCorruptListWithGetcwdInName(): void
    {
        try {
            $tmpDir = __DIR__.'/../../Fixtures/ListFilesTest/using-getcwd';
            $tmpFile = $tmpDir.'/'.ltrim((string) getcwd(), '/').'-out.php';
            self::$filesystem->dumpFile($tmpFile, '<?php function a() {  }');

            $tmpFile = realpath($tmpFile);
            self::assertIsString($tmpFile);
            self::assertFileExists($tmpFile);

            $commandTester = $this->doTestExecute([
                '--config' => __DIR__.'/../../Fixtures/ListFilesTest/.php-cs-fixer.using-getcwd.php',
            ]);
            $expectedPath = str_replace('/', \DIRECTORY_SEPARATOR, './'.Path::makeRelative($tmpFile, (string) getcwd()));
            self::assertSame(0, $commandTester->getStatusCode());
            self::assertSame(escapeshellarg($expectedPath).\PHP_EOL, $commandTester->getDisplay());
        } finally {
            self::$filesystem->remove($tmpDir);
        }
    }

    /**
     * @param array<string, bool|string> $arguments
     */
    private function doTestExecute(array $arguments): CommandTester
    {
        $application = new Application();
        $application->add(new ListFilesCommand(new ToolInfo()));

        $command = $application->find('list-files');
        $commandTester = new CommandTester($command);

        $commandTester->execute($arguments);

        return $commandTester;
    }
}
