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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerOptionBuilder
 */
final class FixerOptionBuilderTest extends TestCase
{
    public function testSetDefault()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setDefault('baz'));
    }

    public function testSetAllowedTypes()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setAllowedTypes(['bool']));
    }

    public function testSetAllowedValues()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $this->assertSame($builder, $builder->setAllowedValues(['baz']));
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
            ->setAllowedTypes(['bool'])
            ->setAllowedValues([true, false])
            ->setNormalizer(function () {})
            ->getOption()
        ;
        $this->assertInstanceOf(\PhpCsFixer\FixerConfiguration\FixerOption::class, $option);
        $this->assertSame('foo', $option->getName());
        $this->assertSame('Bar.', $option->getDescription());
        $this->assertTrue($option->hasDefault());
        $this->assertSame('baz', $option->getDefault());
        $this->assertSame(['bool'], $option->getAllowedTypes());
        $this->assertSame([true, false], $option->getAllowedValues());
        $this->assertInstanceOf(\Closure::class, $option->getNormalizer());
    }
}
