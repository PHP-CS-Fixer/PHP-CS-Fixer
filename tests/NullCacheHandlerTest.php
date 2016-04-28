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

use PhpCsFixer\NullCacheHandler;

class NullCacheHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsCacheHandlerInterface()
    {
        $handler = new NullCacheHandler();

        $this->assertInstanceOf('PhpCsFixer\CacheHandler', $handler);
    }

    public function testWillNotCache()
    {
        $handler = new NullCacheHandler();

        $this->assertFalse($handler->willCache());
    }

    public function testReadReturnsNull()
    {
        $handler = new NullCacheHandler();

        $this->assertNull($handler->read());
    }
}
