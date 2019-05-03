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

namespace PhpCsFixer\Tests;

use PhpCsFixer\StdinFileInfo;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\StdinFileInfo
 */
final class StdinFileInfoTest extends TestCase
{
    public function testToString()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('php://stdin', (string) $fileInfo);
    }

    public function testGetRealPath()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('php://stdin', $fileInfo->getRealPath());
    }

    public function testGetATime()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getATime());
    }

    public function testGetBasename()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('stdin.php', $fileInfo->getBasename());
    }

    public function testGetCTime()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getCTime());
    }

    public function testGetExtension()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('.php', $fileInfo->getExtension());
    }

    public function testGetFileInfo()
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::getFileInfo" is not implemented.');

        $fileInfo->getFileInfo();
    }

    public function testGetFilename()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('stdin.php', $fileInfo->getFilename());
    }

    public function testGetGroup()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getGroup());
    }

    public function testGetInode()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getInode());
    }

    public function testGetLinkTarget()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('', $fileInfo->getLinkTarget());
    }

    public function testGetMTime()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getMTime());
    }

    public function testGetOwner()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getOwner());
    }

    public function testGetPath()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('', $fileInfo->getPath());
    }

    public function testGetPathInfo()
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::getPathInfo" is not implemented.');

        $fileInfo->getPathInfo();
    }

    public function testGetPathname()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('stdin.php', $fileInfo->getPathname());
    }

    public function testGetPerms()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getPerms());
    }

    public function testGetSize()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame(0, $fileInfo->getSize());
    }

    public function testGetType()
    {
        $fileInfo = new StdinFileInfo();

        static::assertSame('file', $fileInfo->getType());
    }

    public function testIsDir()
    {
        $fileInfo = new StdinFileInfo();

        static::assertFalse($fileInfo->isDir());
    }

    public function testIsExecutable()
    {
        $fileInfo = new StdinFileInfo();

        static::assertFalse($fileInfo->isExecutable());
    }

    public function testIsFile()
    {
        $fileInfo = new StdinFileInfo();

        static::assertTrue($fileInfo->isFile());
    }

    public function testIsLink()
    {
        $fileInfo = new StdinFileInfo();

        static::assertFalse($fileInfo->isLink());
    }

    public function testIsReadable()
    {
        $fileInfo = new StdinFileInfo();

        static::assertTrue($fileInfo->isReadable());
    }

    public function testIsWritable()
    {
        $fileInfo = new StdinFileInfo();

        static::assertFalse($fileInfo->isWritable());
    }

    public function testOpenFile()
    {
        $fileInfo = new StdinFileInfo();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "PhpCsFixer\StdinFileInfo::openFile" is not implemented.');

        $fileInfo->openFile();
    }
}
