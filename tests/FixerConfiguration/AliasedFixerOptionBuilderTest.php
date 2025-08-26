<?php

declare(strict_types=1);

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

use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AliasedFixerOptionBuilderTest extends TestCase
{
    public function testSetDefault(): void
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        self::assertSame($builder, $builder->setDefault('baz'));
    }

    public function testSetAllowedTypes(): void
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        self::assertSame($builder, $builder->setAllowedTypes(['bool']));
    }

    public function testSetAllowedValues(): void
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        self::assertSame($builder, $builder->setAllowedValues(['baz']));
    }

    public function testSetNormalizer(): void
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        self::assertSame($builder, $builder->setNormalizer(static fn () => null));
    }

    public function testGetOption(): void
    {
        $builder = new AliasedFixerOptionBuilder(new FixerOptionBuilder('foo', 'Bar.'), 'baz');
        $option = $builder
            ->setDefault('baz')
            ->setAllowedTypes(['bool'])
            ->setAllowedValues([true, false])
            ->setNormalizer(static fn () => null)
            ->getOption()
        ;

        self::assertSame('foo', $option->getName());
        self::assertSame('Bar.', $option->getDescription());
        self::assertTrue($option->hasDefault());
        self::assertSame('baz', $option->getDefault());
        self::assertSame(['bool'], $option->getAllowedTypes());
        self::assertSame([true, false], $option->getAllowedValues());
        self::assertInstanceOf(\Closure::class, $option->getNormalizer());
        self::assertSame('baz', $option->getAlias());
    }
}
