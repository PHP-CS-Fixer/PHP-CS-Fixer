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

    public function testSetDefault()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame($option, $option->setDefault('baz'));
    }

    public function testHasDefault()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertFalse($option->hasDefault());

        $option->setDefault('baz');
        $this->assertTrue($option->hasDefault());
    }

    public function testGetDefault()
    {
        $option = new FixerOption('foo', 'Bar.');
        $option->setDefault('baz');
        $this->assertSame('baz', $option->getDefault());
    }

    public function testGetUndefinedDefault()
    {
        $option = new FixerOption('foo', 'Bar.');

        $this->setExpectedException('LogicException', 'No default value defined.');
        $option->getDefault();
    }

    public function testSetAllowedTypes()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame($option, $option->setAllowedTypes('bool'));
    }

    public function testGetAllowedTypes()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getAllowedTypes());

        $option->setAllowedTypes('bool');
        $this->assertSame(array('bool'), $option->getAllowedTypes());

        $option->setAllowedTypes(array('bool', 'string'));
        $this->assertSame(array('bool', 'string'), $option->getAllowedTypes());
    }

    public function testSetAllowedValues()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame($option, $option->setAllowedValues('baz'));
    }

    public function testGetAllowedValues()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getAllowedValues());

        $option->setAllowedValues('baz');
        $this->assertSame(array('baz'), $option->getAllowedValues());

        $option->setAllowedValues(array('baz', 'qux'));
        $this->assertSame(array('baz', 'qux'), $option->getAllowedValues());

        $function = function () {};
        $option->setAllowedValues($function);
        $this->assertSame(array($function), $option->getAllowedValues());
    }

    public function testSetAllowedValueIsSubsetOf()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame($option, $option->setAllowedValueIsSubsetOf(array('baz', 'qux')));
    }

    public function testAddNormalizer()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertSame($option, $option->setNormalizer(function () {}));
    }

    public function testGetNormalizers()
    {
        $option = new FixerOption('foo', 'Bar.');
        $this->assertNull($option->getNormalizer());

        $normalizer = function () {};
        $option->setNormalizer($normalizer);
        $this->assertSame($normalizer, $option->getNormalizer());
    }
}
