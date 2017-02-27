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

namespace PhpCsFixer\Tests\FixerConfiguration;

use PhpCsFixer\FixerConfiguration\FixerOption;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerOption
 */
final class FixerOptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame('foo', $option->getName());
    }

    public function testGetDescription()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame('Bar.', $option->getDescription());
    }

    public function testHasDefault()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertFalse($option->hasDefault());

        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        $this->assertTrue($option->hasDefault());
    }

    public function testGetDefault()
    {
        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        $this->assertSame('baz', $option->getDefault());
    }

    public function testGetUndefinedDefault()
    {
        $option = new FixerOption('foo', 'Bar.');

        $this->setExpectedException(\LogicException::class, 'No default value defined.');
        $option->getDefault();
    }

    public function testGetAllowedTypes()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool']);
        $this->assertSame(['bool'], $option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool', 'string']);
        $this->assertSame(['bool', 'string'], $option->getAllowedTypes());
    }

    public function testGetAllowedValues()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz']);
        $this->assertSame(['baz'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz', 'qux']);
        $this->assertSame(['baz', 'qux'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, [function () {}]);
        $allowedTypes = $option->getAllowedValues();
        $this->assertInternalType('array', $allowedTypes);
        $this->assertCount(1, $allowedTypes);
        $this->assertArrayHasKey(0, $allowedTypes);
        $this->assertInstanceOf(\Closure::class, $allowedTypes[0]);
    }

    public function testGetNormalizers()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getNormalizer());

        $option = new FixerOption('foo', 'Bar.', true, null, null, null, function () {});
        $this->assertInstanceOf(\Closure::class, $option->getNormalizer());
    }

    public function testRequiredWithDefaultValue()
    {
        $this->setExpectedException(\LogicException::class, 'Required options cannot have a default value.');

        new FixerOption('foo', 'Bar.', true, false);
    }
}
