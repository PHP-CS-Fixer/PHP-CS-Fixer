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
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Cache\SignatureInterface;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\Cache\Cache');

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheInterface()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\Cache\Cache');

        $this->assertTrue($reflection->implementsInterface('PhpCsFixer\Cache\CacheInterface'));
    }

    public function testConstructorSetsValues()
    {
        $signature = $this->getSignatureMock();

        $cache = new Cache($signature);

        $this->assertSame($signature, $cache->getSignature());
    }

    public function testDefaults()
    {
        $signature = $this->getSignatureMock();

        $cache = new Cache($signature);

        $file = 'test.php';

        $this->assertFalse($cache->has($file));
        $this->assertNull($cache->get($file));
    }

    public function testSetThrowsInvalidArgumentExceptionIfValueIsNotAnInteger()
    {
        $this->setExpectedException('InvalidArgumentException');

        $signature = $this->getSignatureMock();

        $cache = new Cache($signature);

        $file = 'test.php';

        $cache->set($file, null);
    }

    public function testCanSetAndGetValue()
    {
        $signature = $this->getSignatureMock();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);

        $this->assertTrue($cache->has($file));
        $this->assertSame($hash, $cache->get($file));
    }

    public function testCanClearValue()
    {
        $signature = $this->getSignatureMock();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);
        $cache->clear($file);

        $this->assertNull($cache->get($file));
    }

    public function testCanSerializeAndDeserialize()
    {
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

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);

        /* @var Cache $unserialized */
        $unserialized = unserialize(serialize($cache));

        $this->assertTrue($unserialized->getSignature()->equals($signature));
        $this->assertTrue($unserialized->has($file));
        $this->assertSame($hash, $unserialized->get($file));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SignatureInterface
     */
    private function getSignatureMock()
    {
        return $this->getMockBuilder('PhpCsFixer\Cache\SignatureInterface')->getMock();
    }
}
