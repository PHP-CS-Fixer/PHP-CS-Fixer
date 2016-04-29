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
use PhpCsFixer\Cache\NullHandler;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class NullHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsHandlerInterface()
    {
        $handler = new NullHandler();

        $this->assertInstanceOf('PhpCsFixer\Cache\HandlerInterface', $handler);
    }

    public function testDefaults()
    {
        $handler = new NullHandler();

        $this->assertNull($handler->file());
    }

    public function testReadReturnsNull()
    {
        $handler = new NullHandler();

        $this->assertNull($handler->read());
    }

    public function testWriteDoesNothing()
    {
        $cache = $this->cacheMock();

        $cache
            ->expects($this->never())
            ->method($this->anything())
        ;

        $handler = new NullHandler();

        $handler->write($cache);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CacheInterface
     */
    private function cacheMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\CacheInterface')->getMock();
    }
}
