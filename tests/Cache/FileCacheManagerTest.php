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
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandlerInterface;
use PhpCsFixer\Cache\SignatureInterface;
use PhpCsFixer\Hasher;
use PhpCsFixer\Tests\TestCase;

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

        self::assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheManagerInterface(): void
    {
        $reflection = new \ReflectionClass(FileCacheManager::class);

        self::assertTrue($reflection->implementsInterface(CacheManagerInterface::class));
    }

    public function testCreatesCacheIfHandlerReturnedNoCache(): void
    {
        $signature = $this->createSignatureDouble(false);
        $handler = $this->createFileHandlerDouble(null);

        $manager = new FileCacheManager($handler, $signature);
        unset($manager);

        self::assertWriteCallCount(1, $handler);
    }

    public function testCreatesCacheIfCachedSignatureIsDifferent(): void
    {
        $cachedSignature = $this->createSignatureDouble(false);
        $signature = $this->createSignatureDouble(false);
        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache);

        $manager = new FileCacheManager($handler, $signature);
        unset($manager);

        self::assertWriteCallCount(1, $handler);
    }

    public function testUsesCacheIfCachedSignatureIsEqualAndNoFileWasUpdated(): void
    {
        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);
        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache);

        $manager = new FileCacheManager($handler, $signature);
        unset($manager);

        self::assertWriteCallCount(0, $handler);
    }

    public function testNeedFixingReturnsTrueIfCacheHasNoHash(): void
    {
        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);
        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache, $this->getFile());

        $manager = new FileCacheManager($handler, $signature);

        self::assertTrue($manager->needFixing('hello.php', '<?php echo "Hello!"'));
    }

    public function testNeedFixingReturnsTrueIfCachedHashIsDifferent(): void
    {
        $file = 'hello.php';

        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);
        $cache = $this->createCacheDouble($cachedSignature, [$file => Hasher::calculate('<?php echo "Hello, old world!";')]);
        $handler = $this->createFileHandlerDouble($cache, $this->getFile());

        $manager = new FileCacheManager($handler, $signature);

        self::assertTrue($manager->needFixing($file, '<?php echo "Hello, new world!";'));
    }

    public function testNeedFixingReturnsFalseIfCachedHashIsIdentical(): void
    {
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);
        $cache = $this->createCacheDouble($cachedSignature, [$file => Hasher::calculate($fileContent)]);
        $handler = $this->createFileHandlerDouble($cache, $this->getFile());

        $manager = new FileCacheManager($handler, $signature);

        self::assertFalse($manager->needFixing($file, $fileContent));
    }

    public function testNeedFixingUsesRelativePathToFile(): void
    {
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';

        $directory = $this->createDirectoryDouble($relativePathToFile);
        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);

        $cache = $this->createCacheDouble($cachedSignature, [$relativePathToFile => Hasher::calculate('<?php echo "Old!"')]);
        $handler = $this->createFileHandlerDouble($cache, $this->getFile());

        $manager = new FileCacheManager($handler, $signature, false, $directory);

        self::assertTrue($manager->needFixing($file, '<?php echo "New!"'));
    }

    public function testSetFileSetsHashOfFileContent(): void
    {
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);
        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache, $cacheFile);

        $manager = new FileCacheManager($handler, $signature);

        self::assertFalse($cache->has($file));

        $manager->setFile($file, $fileContent);

        unset($manager);

        self::assertTrue($cache->has($file));
        self::assertSame(Hasher::calculate($fileContent), $cache->get($file));
        self::assertWriteCallCount(1, $handler);
    }

    public function testSetFileSetsHashOfFileContentDuringDryRunIfCacheHasNoHash(): void
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';

        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);

        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache, $cacheFile);

        self::assertFalse($cache->has($file));

        $manager = new FileCacheManager($handler, $signature, $isDryRun);

        $manager->setFile($file, $fileContent);

        self::assertTrue($cache->has($file));
        self::assertSame(Hasher::calculate($fileContent), $cache->get($file));
    }

    public function testSetFileClearsHashDuringDryRunIfCachedHashIsDifferent(): void
    {
        $isDryRun = true;
        $cacheFile = $this->getFile();
        $file = 'hello.php';
        $fileContent = '<?php echo "Hello!"';
        $previousFileContent = '<?php echo "Hello, world!"';

        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);

        $cache = $this->createCacheDouble($cachedSignature, [$file => Hasher::calculate($previousFileContent)]);
        $handler = $this->createFileHandlerDouble($cache, $cacheFile);

        $manager = new FileCacheManager($handler, $signature, $isDryRun);

        $manager->setFile($file, $fileContent);

        self::assertFalse($cache->has($file));
    }

    public function testSetFileUsesRelativePathToFile(): void
    {
        $cacheFile = $this->getFile();
        $file = '/foo/bar/baz/src/hello.php';
        $relativePathToFile = 'src/hello.php';
        $fileContent = '<?php echo "Hello!"';

        $directory = $this->createDirectoryDouble($relativePathToFile);
        $cachedSignature = $this->createSignatureDouble(true);
        $signature = $this->createSignatureDouble(true);

        $cache = $this->createCacheDouble($cachedSignature);
        $handler = $this->createFileHandlerDouble($cache, $cacheFile);

        $manager = new FileCacheManager($handler, $signature, false, $directory);

        $manager->setFile($file, $fileContent);

        self::assertTrue($cache->has($relativePathToFile));
        self::assertSame(Hasher::calculate($fileContent), $cache->get($relativePathToFile));
    }

    private static function assertWriteCallCount(int $writeCallCount, FileHandlerInterface $handler): void
    {
        self::assertSame(
            $writeCallCount,
            \Closure::bind(
                static fn ($handler): int => $handler->writeCallCount,
                null,
                \get_class($handler)
            )($handler),
        );
    }

    private function getFile(): string
    {
        return __DIR__.'/../Fixtures/.php_cs.empty-cache';
    }

    private function createDirectoryDouble(string $relativePath): DirectoryInterface
    {
        return new class($relativePath) implements DirectoryInterface {
            private string $relativePath;

            public function __construct(string $relativePath)
            {
                $this->relativePath = $relativePath;
            }

            public function getRelativePathTo(string $file): string
            {
                return $this->relativePath;
            }
        };
    }

    private function createSignatureDouble(bool $isEqual): SignatureInterface
    {
        return new class($isEqual) implements SignatureInterface {
            private bool $isEqual;

            public function __construct(bool $isEqual)
            {
                $this->isEqual = $isEqual;
            }

            public function getPhpVersion(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getFixerVersion(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getIndent(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getLineEnding(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getRules(): array
            {
                throw new \LogicException('Not implemented.');
            }

            public function equals(SignatureInterface $signature): bool
            {
                return $this->isEqual;
            }
        };
    }

    /**
     * @param array<string, string> $fileMap
     */
    private function createCacheDouble(SignatureInterface $signature, array $fileMap = []): CacheInterface
    {
        return new class($signature, $fileMap) implements CacheInterface {
            private SignatureInterface $signature;

            /** @var array<string, string> */
            private array $fileMap;

            /**
             * @param array<string, string> $fileMap
             */
            public function __construct(SignatureInterface $signature, array $fileMap)
            {
                $this->signature = $signature;
                $this->fileMap = $fileMap;
            }

            public function getSignature(): SignatureInterface
            {
                return $this->signature;
            }

            public function has(string $file): bool
            {
                return isset($this->fileMap[$file]);
            }

            public function get(string $file): string
            {
                \assert(\array_key_exists($file, $this->fileMap));

                return $this->fileMap[$file];
            }

            public function set(string $file, string $hash): void
            {
                $this->fileMap[$file] = $hash;
            }

            public function clear(string $file): void
            {
                unset($this->fileMap[$file]);
            }

            public function toJson(): string
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }

    private function createFileHandlerDouble(?CacheInterface $cache, ?string $file = null, ?string $signature = null): FileHandlerInterface
    {
        return new class($cache, $file, $signature) implements FileHandlerInterface {
            private ?CacheInterface $cache;
            private ?string $file;
            private ?string $signature;
            private int $writeCallCount = 0;

            public function __construct(?CacheInterface $cache, ?string $file, ?string $signature)
            {
                $this->cache = $cache;
                $this->file = $file;
                $this->signature = $signature;
            }

            public function getFile(): string
            {
                return $this->file;
            }

            public function read(): ?CacheInterface
            {
                return $this->cache;
            }

            public function write(CacheInterface $cache): void
            {
                ++$this->writeCallCount;

                if (null !== $this->signature) {
                    TestCase::assertSame($this->signature, $cache->getSignature());
                }
            }
        };
    }
}
