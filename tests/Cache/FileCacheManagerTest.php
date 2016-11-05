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

use PhpCsFixer\Cache\CacheInterface;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandlerInterface;
use PhpCsFixer\Cache\SignatureInterface;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FileCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\Cache\FileCacheManager');

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheManagerInterface()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\Cache\FileCacheManager');

        $this->assertTrue($reflection->implementsInterface('PhpCsFixer\Cache\CacheManagerInterface'));
    }

    public function testCreatesCacheIfHandlerReturnedNoCache()
    {
        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->never())
            ->method($this->anything())
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn(null)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->logicalAnd(
                $this->isInstanceOf('PhpCsFixer\Cache\CacheInterface'),
                $this->callback(function (CacheInterface $cache) use ($signature) {
                    return $cache->getSignature() === $signature;
                })
            ))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testCreatesCacheIfCachedSignatureIsDifferent()
    {
        $cachedSignature = $this->getSignatureMock();

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(false)
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->logicalAnd(
                $this->isInstanceOf('PhpCsFixer\Cache\CacheInterface'),
                $this->callback(function (CacheInterface $cache) use ($signature) {
                    return $cache->getSignature() === $signature;
                })
            ))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testUsesCacheIfCachedSignatureIsEqual()
    {
        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($cache))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testNeedFixingReturnsTrueIfCacheHasNoHash()
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($file))
            ->willReturn(false)
        ;

        $cache
            ->expects($this->never())
            ->method('get')
            ->with($this->anything())
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        $this->assertTrue($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingReturnsTrueIfCachedHashIsDifferent()
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';
        $previousFileContent = '<?php echo "Hello, world!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($file))
            ->willReturn(true)
        ;

        $cache
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($file))
            ->willReturn(crc32($previousFileContent))
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        $this->assertTrue($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingReturnsFalseIfCachedHashIsIdentical()
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($file))
            ->willReturn(true)
        ;

        $cache
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($file))
            ->willReturn(crc32($fileContent))
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        $this->assertFalse($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingUsesRelativePathToFile()
    {
        $cacheFile = $this->getFile();
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';
        $fileContent = '<?php echo "Hello!"';

        $directory = $this->getDirectoryMock();

        $directory
            ->expects($this->once())
            ->method('getRelativePathTo')
            ->with($this->identicalTo($file))
            ->willReturn($relativePathToFile)
        ;

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($relativePathToFile))
            ->willReturn(true)
        ;

        $cache
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($relativePathToFile))
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->never())
            ->method('getFile')
            ->willReturn($cacheFile)
        ;

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directory
        );

        $manager->needFixing($file, $fileContent);
    }

    public function testSetFileSetsHashOfFileContent()
    {
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->never())
            ->method('has')
            ->with($this->anything())
        ;

        $cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($file),
                $this->identicalTo(crc32($fileContent))
            )
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($cacheFile)
        ;

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($cache))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileSetsHashOfFileContentDuringDryRunIfCacheHasNoHash()
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($file))
            ->willReturn(false)
        ;

        $cache
            ->expects($this->never())
            ->method('get')
            ->with($this->anything())
        ;

        $cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($file),
                $this->identicalTo(crc32($fileContent))
            )
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($cacheFile)
        ;

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($cache))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature,
            $isDryRun
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileClearsHashDuringDryRunIfCachedHashIsDifferent()
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';
        $previousFileContent = '<?php echo "Hello, world!"';

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($file))
            ->willReturn(true)
        ;

        $cache
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($file))
            ->willReturn(crc32($previousFileContent))
        ;

        $cache
            ->expects($this->once())
            ->method('clear')
            ->with($this->identicalTo($file))
        ;

        $cache
            ->expects($this->never())
            ->method('set')
            ->with($this->anything())
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($cacheFile)
        ;

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($cache))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature,
            $isDryRun
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileUsesRelativePathToFile()
    {
        $cacheFile = $this->getFile();
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';
        $fileContent = '<?php echo "Hello!"';

        $directory = $this->getDirectoryMock();

        $directory
            ->expects($this->once())
            ->method('getRelativePathTo')
            ->with($this->identicalTo($file))
            ->willReturn($relativePathToFile)
        ;

        $cachedSignature = $this->getSignatureMock();

        $signature = $this->getSignatureMock();

        $signature
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($cachedSignature))
            ->willReturn(true)
        ;

        $cache = $this->getCacheMock();

        $cache
            ->expects($this->once())
            ->method('getSignature')
            ->willReturn($cachedSignature)
        ;

        $cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($relativePathToFile),
                $this->identicalTo(crc32($fileContent))
            )
        ;

        $handler = $this->getFileHandlerMock();

        $handler
            ->expects($this->never())
            ->method('getFile')
            ->willReturn($cacheFile)
        ;

        $handler
            ->expects($this->once())
            ->method('read')
            ->willReturn($cache)
        ;

        $handler
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($cache))
        ;

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directory
        );

        $manager->setFile($file, $fileContent);
    }

    /**
     * @return string
     */
    private function getFile()
    {
        return __DIR__.'/.php_cs.cache';
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FileHandlerInterface
     */
    private function getFileHandlerMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\FileHandlerInterface')->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CacheInterface
     */
    private function getCacheMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\CacheInterface')->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SignatureInterface
     */
    private function getSignatureMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\SignatureInterface')->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DirectoryInterface
     */
    private function getDirectoryMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\DirectoryInterface')->getMock();
    }
}
