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

use PhpCsFixer\Cache\Cache;
use PhpCsFixer\Cache\FileHandler;
use PhpCsFixer\Cache\Signature;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FileHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        $file = $this->file();

        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function testImplementsHandlerInterface()
    {
        $file = $this->file();

        $handler = new FileHandler($file);

        $this->assertInstanceOf('PhpCsFixer\Cache\HandlerInterface', $handler);
    }

    public function testConstructorSetsFile()
    {
        $file = $this->file();

        $handler = new FileHandler($file);

        $this->assertSame($file, $handler->file());
    }

    public function testReadReturnsNullIfFileDoesNotExist()
    {
        $file = $this->file();

        $handler = new FileHandler($file);

        $this->assertNull($handler->read());
    }

    public function testReadReturnsNullIfContentCanNotBeDeserialized()
    {
        $file = $this->file();

        file_put_contents($file, 'hello');

        $handler = new FileHandler($file);

        $this->assertNull($handler->read());
    }

    public function testReadReturnsCache()
    {
        $file = $this->file();

        $signature = new Signature(
            PHP_VERSION,
            '2.0',
            true,
            array(
                'foo',
                'bar',
            )
        );

        $cache = new Cache($signature);

        file_put_contents($file, serialize($cache));

        $handler = new FileHandler($file);

        $cached = $handler->read();

        $this->assertInstanceOf('PhpCsFixer\Cache\CacheInterface', $cache);
        $this->assertTrue($cached->signature()->equals($signature));
    }

    public function testWriteThrowsIOExceptionIfFileCanNotBeWritten()
    {
        $file = __DIR__.'/non-existent-directory/.php_cs.cache';

        $this->setExpectedException('Symfony\Component\Filesystem\Exception\IOException', sprintf(
            'Failed to write file "%s".',
            $file
        ));

        $cache = new Cache(new Signature(
            PHP_VERSION,
            '2.0',
            true,
            array(
                'foo',
                'bar',
            )
        ));

        $handler = new FileHandler($file);

        $handler->write($cache);
    }

    public function testWriteWritesCache()
    {
        $file = $this->file();

        $cache = new Cache(new Signature(
            PHP_VERSION,
            '2.0',
            true,
            array(
                'foo',
                'bar',
            )
        ));

        $handler = new FileHandler($file);

        $handler->write($cache);

        $this->assertFileExists($file);
        $this->assertSame(serialize($cache), file_get_contents($file));
    }

    /**
     * @return string
     */
    private function file()
    {
        return __DIR__.'/.php_cs.cache';
    }
}
