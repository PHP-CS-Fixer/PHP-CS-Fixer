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

use PhpCsFixer\StdinFileInfo;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\StdinFileInfo
 *
 * @author ntzm
 */
final class StdinFileInfoTest extends TestCase
{
    public function testToString(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('php://stdin', (string) $fileInfo);
    }

    public function testGetRealPath(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('php://stdin', $fileInfo->getRealPath());
    }

    public function testGetATime(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getATime());
    }

    public function testGetBasename(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('stdin.php', $fileInfo->getBasename());
    }

    public function testGetCTime(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getCTime());
    }

    public function testGetExtension(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('.php', $fileInfo->getExtension());
    }

    public function testGetFileInfo(): void
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::getFileInfo" is not implemented.');

        $fileInfo->getFileInfo();
    }

    public function testGetFilename(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('stdin.php', $fileInfo->getFilename());
    }

    public function testGetGroup(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getGroup());
    }

    public function testGetInode(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getInode());
    }

    public function testGetLinkTarget(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('', $fileInfo->getLinkTarget());
    }

    public function testGetMTime(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getMTime());
    }

    public function testGetOwner(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getOwner());
    }

    public function testGetPath(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('', $fileInfo->getPath());
    }

    public function testGetPathInfo(): void
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::getPathInfo" is not implemented.');

        $fileInfo->getPathInfo();
    }

    public function testGetPathname(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('stdin.php', $fileInfo->getPathname());
    }

    public function testGetPerms(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getPerms());
    }

    public function testGetSize(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame(0, $fileInfo->getSize());
    }

    public function testGetType(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertSame('file', $fileInfo->getType());
    }

    public function testIsDir(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertFalse($fileInfo->isDir());
    }

    public function testIsExecutable(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertFalse($fileInfo->isExecutable());
    }

    public function testIsFile(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertTrue($fileInfo->isFile());
    }

    public function testIsLink(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertFalse($fileInfo->isLink());
    }

    public function testIsReadable(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertTrue($fileInfo->isReadable());
    }

    public function testIsWritable(): void
    {
        $fileInfo = new StdinFileInfo();

        self::assertFalse($fileInfo->isWritable());
    }

    public function testOpenFile(): void
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::openFile" is not implemented.');

        $fileInfo->openFile();
    }
}
