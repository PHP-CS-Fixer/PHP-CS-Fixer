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

use PhpCsFixer\Cache\Cache;
use PhpCsFixer\Cache\FileHandler;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Cache\SignatureInterface;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\FileHandler
 */
final class FileHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $file = $this->getFile();

        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function testImplementsHandlerInterface(): void
    {
        $file = $this->getFile();

        $handler = new FileHandler($file);

        static::assertInstanceOf(\PhpCsFixer\Cache\FileHandlerInterface::class, $handler);
    }

    public function testConstructorSetsFile(): void
    {
        $file = $this->getFile();

        $handler = new FileHandler($file);

        static::assertSame($file, $handler->getFile());
    }

    public function testReadReturnsNullIfFileDoesNotExist(): void
    {
        $file = $this->getFile();

        $handler = new FileHandler($file);

        static::assertNull($handler->read());
    }

    public function testReadReturnsNullIfContentCanNotBeDeserialized(): void
    {
        $file = $this->getFile();

        file_put_contents($file, 'hello');

        $handler = new FileHandler($file);

        static::assertNull($handler->read());
    }

    public function testReadReturnsCache(): void
    {
        $file = $this->getFile();

        $signature = $this->createSignature();

        $cache = new Cache($signature);

        file_put_contents($file, $cache->toJson());

        $handler = new FileHandler($file);

        $cached = $handler->read();

        static::assertInstanceOf(\PhpCsFixer\Cache\CacheInterface::class, $cached);
        static::assertTrue($cached->getSignature()->equals($signature));
    }

    public function testWriteThrowsIOExceptionIfFileCanNotBeWritten(): void
    {
        $file = __DIR__.'/non-existent-directory/.php-cs-fixer.cache';

        $this->expectException(IOException::class);
        $this->expectExceptionMessageMatches(sprintf(
            '#^Directory of cache file "%s" does not exists.#',
            preg_quote($file, '#')
        ));

        $cache = new Cache($this->createSignature());

        $handler = new FileHandler($file);

        $handler->write($cache);
    }

    public function testWriteWritesCache(): void
    {
        $file = $this->getFile();

        $cache = new Cache($this->createSignature());

        $handler = new FileHandler($file);

        $handler->write($cache);

        static::assertFileExists($file);

        $actualCacheJson = file_get_contents($file);

        static::assertSame($cache->toJson(), $actualCacheJson);
    }

    public function testWriteCacheToDirectory(): void
    {
        $dir = __DIR__.'/../Fixtures/cache-file-handler';

        $handler = new FileHandler($dir);

        $this->expectException(IOException::class);
        $this->expectExceptionMessageMatches(sprintf(
            '#^%s$#',
            preg_quote('Cannot write cache file "'.realpath($dir).'" as the location exists as directory.', '#')
        ));

        $handler->write(new Cache($this->createSignature()));
    }

    public function testWriteCacheToNonWriteableFile(): void
    {
        $file = __DIR__.'/../Fixtures/cache-file-handler/cache-file';
        if (is_writable($file)) {
            static::markTestSkipped(sprintf('File "%s" must be not writeable for this tests.', realpath($file)));
        }

        $handler = new FileHandler($file);

        $this->expectException(IOException::class);
        $this->expectExceptionMessageMatches(sprintf(
            '#^%s$#',
            preg_quote('Cannot write to file "'.realpath($file).'" as it is not writable.', '#')
        ));

        $handler->write(new Cache($this->createSignature()));
    }

    public function testWriteCacheFilePermissions(): void
    {
        $file = __DIR__.'/../Fixtures/cache-file-handler/rw_cache.test';
        @unlink($file);

        static::assertFileDoesNotExist($file);

        $handler = new FileHandler($file);
        $handler->write(new Cache($this->createSignature()));

        static::assertFileExists($file);
        static::assertTrue(@is_file($file), sprintf('Failed cache "%s" `is_file`.', $file));
        static::assertTrue(@is_writable($file), sprintf('Failed cache "%s" `is_writable`.', $file));
        static::assertTrue(@is_readable($file), sprintf('Failed cache "%s" `is_readable`.', $file));

        @unlink($file);
    }

    private function getFile(): string
    {
        return __DIR__.'/.php-cs-fixer.cache';
    }

    private function createSignature(): SignatureInterface
    {
        return new Signature(
            PHP_VERSION,
            '2.0',
            '    ',
            PHP_EOL,
            ['foo' => true, 'bar' => false],
        );
    }
}
