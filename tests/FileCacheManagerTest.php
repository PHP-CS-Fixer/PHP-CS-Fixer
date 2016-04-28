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

use PhpCsFixer\CacheHandler;
use PhpCsFixer\FileCacheManager;

/**
 * @author Andreas Möller <am@localheinz.com>
 */
class FileCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesNotReadFromOrWriteToCache()
    {
        $cacheHandler = $this->cacheHandlerMock();

        $cacheHandler
            ->expects($this->any())
            ->method('willCache')
            ->willReturn(false)
        ;

        $cacheHandler
            ->expects($this->never())
            ->method('read')
        ;

        $cacheHandler
            ->expects($this->never())
            ->method('write')
        ;

        $fileCacheManager = new FileCacheManager(
            $cacheHandler,
            '.php_cs.cache',
            true,
            array()
        );

        $fileCacheManager->setFile('example.php', 'hello');

        unset($fileCacheManager);
    }

    public function testReadsFromAndWritesToCache()
    {
        $cacheHandler = $this->cacheHandlerMock();

        $cacheHandler
            ->expects($this->any())
            ->method('willCache')
            ->willReturn(true)
        ;

        $cacheHandler
            ->expects($this->once())
            ->method('read')
            ->willReturn(null)
        ;

        $cacheHandler
            ->expects($this->once())
            ->method('write')
        ;

        $fileCacheManager = new FileCacheManager(
            $cacheHandler,
            '.php_cs.cache',
            true,
            array()
        );

        $fileCacheManager->setFile('example.php', 'hello');

        unset($fileCacheManager);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CacheHandler
     */
    private function cacheHandlerMock()
    {
        return $this->getMockBuilder('PhpCsFixer\CacheHandler')->getMock();
    }
}
