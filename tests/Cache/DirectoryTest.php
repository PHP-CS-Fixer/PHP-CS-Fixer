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

namespace PhpCsFixer\Tests\Cache;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Directory
 */
final class DirectoryTest extends TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Directory::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsDirectoryInterface()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Directory::class);

        $this->assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\DirectoryInterface::class));
    }

    public function testGetRelativePathToReturnsFileIfAboveLevelOfDirectoryName()
    {
        $directoryName = __DIR__.DIRECTORY_SEPARATOR.'foo';
        $file = __DIR__.DIRECTORY_SEPARATOR.'hello.php';

        $directory = new Directory($directoryName);

        $this->assertSame($file, $directory->getRelativePathTo($file));
    }

    public function testGetRelativePathToReturnsRelativePathIfWithinDirectoryName()
    {
        $directoryName = __DIR__.DIRECTORY_SEPARATOR.'foo';
        $file = __DIR__.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar'.DIRECTORY_SEPARATOR.'hello.php';

        $directory = new Directory($directoryName);

        $this->assertSame('bar'.DIRECTORY_SEPARATOR.'hello.php', $directory->getRelativePathTo($file));
    }
}
