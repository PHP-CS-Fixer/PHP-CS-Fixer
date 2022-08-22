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

namespace PhpCsFixer\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpCsFixer\FileRemoval;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FileRemoval
 */
final class FileRemovalTest extends TestCase
{
    /**
     * Should temporary files be removed on tear down?
     *
     * This is necessary for testShutdownRemovesObserved files, as the setup
     * runs in a separate process to trigger the shutdown function, and
     * tearDownAfterClass is called for every separate process
     *
     * @var bool
     */
    private static $removeFilesOnTearDown = true;

    public static function tearDownAfterClass(): void
    {
        if (self::$removeFilesOnTearDown) {
            @unlink(sys_get_temp_dir().'/cs_fixer_foo.php');
            @unlink(sys_get_temp_dir().'/cs_fixer_bar.php');
        }
    }

    /**
     * @runInSeparateProcess
     *
     * @doesNotPerformAssertions
     */
    public function testShutdownRemovesObservedFilesSetup(): void
    {
        self::$removeFilesOnTearDown = false;

        $fileToBeDeleted = sys_get_temp_dir().'/cs_fixer_foo.php';
        $fileNotToBeDeleted = sys_get_temp_dir().'/cs_fixer_bar.php';

        file_put_contents($fileToBeDeleted, '');
        file_put_contents($fileNotToBeDeleted, '');

        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($fileToBeDeleted);
    }

    /**
     * @depends testShutdownRemovesObservedFilesSetup
     */
    public function testShutdownRemovesObservedFiles(): void
    {
        static::assertFileDoesNotExist(sys_get_temp_dir().'/cs_fixer_foo.php');
        static::assertFileExists(sys_get_temp_dir().'/cs_fixer_bar.php');
    }

    public function testCleanRemovesObservedFiles(): void
    {
        $fs = $this->getMockFileSystem();

        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($fs->url().'/foo.php');
        $fileRemoval->observe($fs->url().'/baz.php');

        $fileRemoval->clean();

        static::assertFileDoesNotExist($fs->url().'/foo.php');
        static::assertFileDoesNotExist($fs->url().'/baz.php');
        static::assertFileExists($fs->url().'/bar.php');
    }

    public function testDestructRemovesObservedFiles(): void
    {
        $fs = $this->getMockFileSystem();

        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($fs->url().'/foo.php');
        $fileRemoval->observe($fs->url().'/baz.php');

        $fileRemoval->__destruct();

        static::assertFileDoesNotExist($fs->url().'/foo.php');
        static::assertFileDoesNotExist($fs->url().'/baz.php');
        static::assertFileExists($fs->url().'/bar.php');
    }

    public function testDeleteObservedFile(): void
    {
        $fs = $this->getMockFileSystem();

        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($fs->url().'/foo.php');
        $fileRemoval->observe($fs->url().'/baz.php');

        $fileRemoval->delete($fs->url().'/foo.php');

        static::assertFileDoesNotExist($fs->url().'/foo.php');
        static::assertFileExists($fs->url().'/baz.php');
    }

    public function testDeleteNonObservedFile(): void
    {
        $fs = $this->getMockFileSystem();

        $fileRemoval = new FileRemoval();

        $fileRemoval->delete($fs->url().'/foo.php');

        static::assertFileDoesNotExist($fs->url().'/foo.php');
    }

    public function testSleep(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize PhpCsFixer\FileRemoval');

        $fileRemoval = new FileRemoval();
        $fileRemoval->__sleep();
    }

    public function testWakeup(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize PhpCsFixer\FileRemoval');

        $fileRemoval = new FileRemoval();
        $fileRemoval->__wakeup();
    }

    private function getMockFileSystem(): vfsStreamDirectory
    {
        return vfsStream::setup('root', null, [
            'foo.php' => '',
            'bar.php' => '',
            'baz.php' => '',
        ]);
    }
}
