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

use PhpCsFixer\Cache\CacheInterface;
use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandlerInterface;
use PhpCsFixer\Cache\SignatureInterface;
use PhpCsFixer\Tests\TestCase;
use Prophecy\Argument;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\FileCacheManager
 */
final class FileCacheManagerTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(FileCacheManager::class);

        static::assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheManagerInterface(): void
    {
        $reflection = new \ReflectionClass(FileCacheManager::class);

        static::assertTrue($reflection->implementsInterface(CacheManagerInterface::class));
    }

    public function testCreatesCacheIfHandlerReturnedNoCache(): void
    {
        $signature = $this->prophesize(SignatureInterface::class)->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->shouldBeCalled()->willReturn(null);
        $handlerProphecy->write(Argument::that(static function (CacheInterface $cache) use ($signature): bool {
            return $cache->getSignature() === $signature;
        }))->shouldBeCalled();
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testCreatesCacheIfCachedSignatureIsDifferent(): void
    {
        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->shouldBeCalled()->willReturn(false);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->shouldBeCalled()->willReturn($cachedSignature);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->shouldBeCalled()->willReturn($cache);
        $handlerProphecy->write(Argument::that(static function (CacheInterface $cache) use ($signature): bool {
            return $cache->getSignature() === $signature;
        }))->shouldBeCalled();
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testUsesCacheIfCachedSignatureIsEqual(): void
    {
        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->shouldBeCalled()->willReturn($cachedSignature);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->shouldBeCalled()->willReturn($cache);
        $handlerProphecy->write(Argument::is($cache))->shouldBeCalled();
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        unset($manager);
    }

    public function testNeedFixingReturnsTrueIfCacheHasNoHash(): void
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(false);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        static::assertTrue($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingReturnsTrueIfCachedHashIsDifferent(): void
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';
        $previousFileContent = '<?php echo "Hello, world!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(md5($previousFileContent));
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        static::assertTrue($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingReturnsFalseIfCachedHashIsIdentical(): void
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(md5($fileContent));
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($this->getFile());
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        static::assertFalse($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingUsesRelativePathToFile(): void
    {
        $cacheFile = $this->getFile();
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';
        $fileContent = '<?php echo "Hello!"';

        $directoryProphecy = $this->prophesize(\PhpCsFixer\Cache\DirectoryInterface::class);
        $directoryProphecy->getRelativePathTo(Argument::is($file))->willReturn($relativePathToFile);

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($relativePathToFile))->willReturn(true);
        $cacheProphecy->has(Argument::is($relativePathToFile))->willReturn(0);
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directoryProphecy->reveal()
        );

        static::assertTrue($manager->needFixing($file, $fileContent));
    }

    public function testSetFileSetsHashOfFileContent(): void
    {
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->set(Argument::is($file), Argument::is(md5($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileSetsHashOfFileContentDuringDryRunIfCacheHasNoHash(): void
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(false);
        $cacheProphecy->set(Argument::is($file), Argument::is(md5($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            $isDryRun
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileClearsHashDuringDryRunIfCachedHashIsDifferent(): void
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';
        $previousFileContent = '<?php echo "Hello, world!"';

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->has(Argument::is($file))->willReturn(true);
        $cacheProphecy->get(Argument::is($file))->willReturn(md5($previousFileContent));
        $cacheProphecy->clear(Argument::is($file))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            $isDryRun
        );

        $manager->setFile($file, $fileContent);
    }

    public function testSetFileUsesRelativePathToFile(): void
    {
        $cacheFile = $this->getFile();
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';
        $fileContent = '<?php echo "Hello!"';

        $directoryProphecy = $this->prophesize(\PhpCsFixer\Cache\DirectoryInterface::class);
        $directoryProphecy->getRelativePathTo(Argument::is($file))->willReturn($relativePathToFile);

        $cachedSignature = $this->prophesize(SignatureInterface::class)->reveal();

        $signatureProphecy = $this->prophesize(SignatureInterface::class);
        $signatureProphecy->equals(Argument::is($cachedSignature))->willReturn(true);
        $signature = $signatureProphecy->reveal();

        $cacheProphecy = $this->prophesize(CacheInterface::class);
        $cacheProphecy->getSignature()->willReturn($cachedSignature);
        $cacheProphecy->set(Argument::is($relativePathToFile), Argument::is(md5($fileContent)))->shouldBeCalled();
        $cache = $cacheProphecy->reveal();

        $handlerProphecy = $this->prophesize(FileHandlerInterface::class);
        $handlerProphecy->read()->willReturn($cache);
        $handlerProphecy->getFile()->willReturn($cacheFile);
        $handlerProphecy->write(Argument::is($cache));
        $handler = $handlerProphecy->reveal();

        $manager = new FileCacheManager(
            $handler,
            $signature,
            false,
            $directoryProphecy->reveal()
        );

        $manager->setFile($file, $fileContent);
    }

    private function getFile(): string
    {
        return __DIR__.'/../Fixtures/.php_cs.empty-cache';
    }
}
