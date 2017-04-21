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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Test\AccessibleObject;
use PhpCsFixer\Tests\Fixtures\Test\AccessibleObjectTest\DummyClass;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Test\AccessibleObject
 */
final class AccessibleObjectTest extends \PHPUnit_Framework_TestCase
{
    protected $accessibleObject;

    protected function setUp()
    {
        $this->accessibleObject = new AccessibleObject(new DummyClass());
    }

    public function testCreate()
    {
        $object = AccessibleObject::create(new \stdClass());

        $this->assertInstanceOf(\PhpCsFixer\Test\AccessibleObject::class, $object);
    }

    public function testGet()
    {
        $this->assertSame('publicVar_value', $this->accessibleObject->publicVar);
        $this->assertSame('privateVar_value', $this->accessibleObject->privateVar);
    }

    public function testSet()
    {
        $this->accessibleObject->publicVar = 'newValue1';
        $this->accessibleObject->privateVar = 'newValue2';

        $this->assertSame('newValue1', $this->accessibleObject->publicVar);
        $this->assertSame('newValue2', $this->accessibleObject->privateVar);
    }

    public function testIsset()
    {
        $this->assertTrue(isset($this->accessibleObject->publicVar));
        $this->assertTrue(isset($this->accessibleObject->privateVar));
        $this->assertFalse(isset($this->accessibleObject->nonExistingVar));
    }

    public function testCall()
    {
        $this->assertSame('publicMethod_result', $this->accessibleObject->publicMethod());
        $this->assertSame('privateMethod_result', $this->accessibleObject->privateMethod());
    }
}
