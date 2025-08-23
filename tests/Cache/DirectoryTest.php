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

namespace PhpCsFixer\Tests\Cache;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Directory
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DirectoryTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(Directory::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testImplementsDirectoryInterface(): void
    {
        $reflection = new \ReflectionClass(Directory::class);

        self::assertTrue($reflection->implementsInterface(DirectoryInterface::class));
    }

    public function testGetRelativePathToReturnsFileIfAboveLevelOfDirectoryName(): void
    {
        $directoryName = __DIR__.\DIRECTORY_SEPARATOR.'foo';
        $file = __DIR__.\DIRECTORY_SEPARATOR.'hello.php';

        $directory = new Directory($directoryName);

        self::assertSame($file, $directory->getRelativePathTo($file));
    }

    public function testGetRelativePathToReturnsRelativePathIfWithinDirectoryName(): void
    {
        $directoryName = __DIR__.\DIRECTORY_SEPARATOR.'foo';
        $file = __DIR__.\DIRECTORY_SEPARATOR.'foo'.\DIRECTORY_SEPARATOR.'bar'.\DIRECTORY_SEPARATOR.'hello.php';

        $directory = new Directory($directoryName);

        self::assertSame('bar'.\DIRECTORY_SEPARATOR.'hello.php', $directory->getRelativePathTo($file));
    }
}
