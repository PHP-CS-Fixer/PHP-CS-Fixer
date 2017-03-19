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

use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @internal
 */
final class FixerOptionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefault()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setDefault('baz'));
    }

    public function testSetAllowedTypes()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setAllowedTypes(array('bool')));
    }

    public function testSetAllowedValues()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setAllowedValues(array('baz')));
    }

    public function testSetNormalizer()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setNormalizer(function () {}));
    }

    public function testGetOption()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $option = $builder
            ->setDefault('baz')
            ->setAllowedTypes(array('bool'))
            ->setAllowedValues(array(true, false))
            ->setNormalizer(function () {})
            ->getOption()
        ;
        $this->assertInstanceOf('PhpCsFixer\FixerConfiguration\FixerOption', $option);
        $this->assertSame('foo', $option->getName());
        $this->assertSame('Bar.', $option->getDescription());
        $this->assertTrue($option->hasDefault());
        $this->assertSame('baz', $option->getDefault());
        $this->assertSame(array('bool'), $option->getAllowedTypes());
        $this->assertSame(array(true, false), $option->getAllowedValues());
        $this->assertInstanceOf('Closure', $option->getNormalizer());
    }
}
