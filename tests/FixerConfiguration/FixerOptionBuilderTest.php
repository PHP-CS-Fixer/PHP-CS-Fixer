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
use PhpCsFixer\Tests\TestCase;

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
        static::assertSame($builder, $builder->setDefault('baz'));
    }

    public function testSetAllowedTypes()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        static::assertSame($builder, $builder->setAllowedTypes(['bool']));
    }

    public function testSetAllowedValues()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        static::assertSame($builder, $builder->setAllowedValues(['baz']));
    }

    public function testSetNormalizer()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        static::assertSame($builder, $builder->setNormalizer(static function () {}));
    }

    public function testGetOption()
    {
        $builder = new FixerOptionBuilder('foo', 'Bar.');
        $option = $builder
            ->setDefault('baz')
            ->setAllowedTypes(['bool'])
            ->setAllowedValues([true, false])
            ->setNormalizer(static function () {})
            ->getOption()
        ;
        static::assertInstanceOf(\PhpCsFixer\FixerConfiguration\FixerOption::class, $option);
        static::assertSame('foo', $option->getName());
        static::assertSame('Bar.', $option->getDescription());
        static::assertTrue($option->hasDefault());
        static::assertSame('baz', $option->getDefault());
        static::assertSame(['bool'], $option->getAllowedTypes());
        static::assertSame([true, false], $option->getAllowedValues());
        static::assertInstanceOf(\Closure::class, $option->getNormalizer());
    }
}
