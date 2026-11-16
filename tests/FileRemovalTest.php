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

use PhpCsFixer\FileRemoval;
use PhpCsFixer\Tests\Test\TestCaseUtils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FileRemoval
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(FileRemoval::class)]
final class FileRemovalTest extends TestCase
{
    /**
     * Should temporary files be removed on tear down?
     *
     * This is necessary for testShutdownRemovesObserved files, as the setup
     * runs in a separate process to trigger the shutdown function, and
     * tearDownAfterClass is called for every separate process
     */
    private static bool $removeFilesOnTearDown = true;

    private string $directory;

    public static function tearDownAfterClass(): void
    {
        if (self::$removeFilesOnTearDown) {
            @unlink(sys_get_temp_dir().'/cs_fixer_foo.php');
            @unlink(sys_get_temp_dir().'/cs_fixer_bar.php');
        }

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = TestCaseUtils::createTemporaryDirectory();

        foreach (['foo.php', 'bar.php', 'baz.php'] as $file) {
            file_put_contents($this->directory.'/'.$file, '');
        }
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);

        parent::tearDown();
    }

    public function testCleanRemovesObservedFiles(): void
    {
        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($this->directory.'/foo.php');
        $fileRemoval->observe($this->directory.'/baz.php');

        $fileRemoval->clean();

        self::assertFileDoesNotExist($this->directory.'/foo.php');
        self::assertFileDoesNotExist($this->directory.'/baz.php');
        self::assertFileExists($this->directory.'/bar.php');
    }

    public function testDestructRemovesObservedFiles(): void
    {
        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($this->directory.'/foo.php');
        $fileRemoval->observe($this->directory.'/baz.php');

        $fileRemoval->__destruct();

        self::assertFileDoesNotExist($this->directory.'/foo.php');
        self::assertFileDoesNotExist($this->directory.'/baz.php');
        self::assertFileExists($this->directory.'/bar.php');
    }

    public function testDeleteObservedFile(): void
    {
        $fileRemoval = new FileRemoval();

        $fileRemoval->observe($this->directory.'/foo.php');
        $fileRemoval->observe($this->directory.'/baz.php');

        $fileRemoval->delete($this->directory.'/foo.php');

        self::assertFileDoesNotExist($this->directory.'/foo.php');
        self::assertFileExists($this->directory.'/baz.php');
    }

    public function testDeleteNonObservedFile(): void
    {
        $fileRemoval = new FileRemoval();

        $fileRemoval->delete($this->directory.'/foo.php');

        self::assertFileDoesNotExist($this->directory.'/foo.php');
    }

    public function testSerialize(): void
    {
        $fileRemoval = new FileRemoval();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize '.FileRemoval::class);

        serialize($fileRemoval);
    }

    public function testUnserialize(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize '.FileRemoval::class);

        unserialize(self::createSerializedStringOfClassName(FileRemoval::class));
    }

    /**
     * Must NOT be run as first test, see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/7104.
     *
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @doesNotPerformAssertions
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DoesNotPerformAssertions]
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
    #[Depends('testShutdownRemovesObservedFilesSetup')]
    public function testShutdownRemovesObservedFiles(): void
    {
        self::assertFileDoesNotExist(sys_get_temp_dir().'/cs_fixer_foo.php');
        self::assertFileExists(sys_get_temp_dir().'/cs_fixer_bar.php');
    }
}
