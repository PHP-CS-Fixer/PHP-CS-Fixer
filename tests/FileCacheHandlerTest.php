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

namespace PhpCsFixer\Test;

use PhpCsFixer\FileCacheHandler;

class FileCacheHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        $cacheFile = $this->cacheFile();

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function testImplementsCacheHandlerInterface()
    {
        $cacheFile = $this->cacheFile();

        $handler = new FileCacheHandler($cacheFile);

        $this->assertInstanceOf('PhpCsFixer\CacheHandler', $handler);
    }

    public function testWillCache()
    {
        $cacheFile = $this->cacheFile();

        $handler = new FileCacheHandler($cacheFile);

        $this->assertTrue($handler->willCache());
    }

    public function testReadReturnsNullIfFileDoesNotExist()
    {
        $cacheFile = $this->cacheFile();

        $handler = new FileCacheHandler($cacheFile);

        $this->assertNull($handler->read());
    }

    public function testReadReturnsFileContent()
    {
        $cacheFile = $this->cacheFile();

        $content = 'foo';

        file_put_contents($cacheFile, $content);

        $handler = new FileCacheHandler($cacheFile);

        $this->assertSame($content, $handler->read());
    }

    public function testWriteWritesFileContent()
    {
        $cacheFile = $this->cacheFile();

        $content = 'foo';

        $handler = new FileCacheHandler($cacheFile);

        $handler->write($content);

        $this->assertFileExists($cacheFile);
        $this->assertSame($content, file_get_contents($cacheFile));
    }

    /**
     * @return string
     */
    private function cacheFile()
    {
        return __DIR__.'/.php_cs.cache';
    }
}
