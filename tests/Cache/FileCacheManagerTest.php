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
use PhpCsFixer\Cache\FileCacheManager;
use Prophecy\Argument;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\FileCacheManager
 */
final class FileCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\FileCacheManager::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheManagerInterface()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\FileCacheManager::class);

        $this->assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\CacheManagerInterface::class));
    }

    public function testCreatesCacheIfHandlerReturnedNoCache()
    {
        $signature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn(null);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::that(function (CacheInterface $cache) use ($signature) {
            return $cache->getSignature() === $signature;
        }))->willReturn(null);
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testCreatesCacheIfCachedSignatureIsDifferent()
    {
        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(false);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::that(function (CacheInterface $cache) use ($signature) {
            return $cache->getSignature() === $signature;
        }))->willReturn(null);
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testUsesCacheIfCachedSignatureIsEqual()
    {
        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(false);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(crc32($previousFileContent));
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(crc32($fileContent));
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $directoryProphecy = $this->prophesize(\PhpCsFixer\Cache\DirectoryInterface::class);
        $directoryProphecy->getRelativePathTo(Argument::is($file))->willReturn($relativePathToFile);

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($relativePathToFile))->willReturn(true);
        $cacheProphecy->has(Argument::is($relativePathToFile))->willReturn(0);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directoryProphecy->reveal()
        );

        $manager->needFixing($file, $fileContent);
    }

    public function testSetFileSetsHashOfFileContent()
    {
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->set(Argument::is($file), Argument::is(crc32($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(false);
        $cacheProphecy->set(Argument::is($file), Argument::is(crc32($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(crc32($previousFileContent));
        $cacheProphecy->clear(Argument::is($file))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

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

        $directoryProphecy = $this->prophesize(\PhpCsFixer\Cache\DirectoryInterface::class);
        $directoryProphecy->getRelativePathTo(Argument::is($file))->willReturn($relativePathToFile);

        $cachedSignature = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(\PhpCsFixer\Cache\CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->set(Argument::is($relativePathToFile), Argument::is(crc32($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(\PhpCsFixer\Cache\FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache))->willReturn(null);
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directoryProphecy->reveal()
        );

        $manager->setFile($file, $fileContent);
    }

    /**
     * @return string
     */
    private function getFile()
    {
        return __DIR__.'/../Fixtures/.php_cs.empty-cache';
    }
}
