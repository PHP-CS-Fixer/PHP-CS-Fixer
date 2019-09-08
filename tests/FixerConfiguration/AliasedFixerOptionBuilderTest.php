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

use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder
 */
final class AliasedFixerOptionBuilderTest extends TestCase
{
    public function testSetDefault()
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        static::assertSame($builder, $builder->setDefault('baz'));
    }

    public function testSetAllowedTypes()
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        static::assertSame($builder, $builder->setAllowedTypes(['bool']));
    }

    public function testSetAllowedValues()
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        static::assertSame($builder, $builder->setAllowedValues(['baz']));
    }

    public function testSetNormalizer()
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        static::assertSame($builder, $builder->setNormalizer(function () {}));
    }

    public function testGetOption()
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        $option = $builder
            ->setDefault('baz')
            ->setAllowedTypes(['bool'])
            ->setAllowedValues([true, false])
            ->setNormalizer(function () {})
            ->getOption()
        ;
        static::assertInstanceOf(AliasedFixerOption::class, $option);
        static::assertSame('foo', $option->getName());
        static::assertSame('Bar.', $option->getDescription());
        static::assertTrue($option->hasDefault());
        static::assertSame('baz', $option->getDefault());
        static::assertSame(['bool'], $option->getAllowedTypes());
        static::assertSame([true, false], $option->getAllowedValues());
        static::assertInstanceOf(\Closure::class, $option->getNormalizer());
        static::assertSame('baz', $option->getAlias());
    }
}
