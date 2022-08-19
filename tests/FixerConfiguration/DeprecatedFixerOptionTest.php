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

use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\DeprecatedFixerOption
 */
final class DeprecatedFixerOptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertInstanceOf(FixerOptionInterface::class, $option);
        static::assertInstanceOf(DeprecatedFixerOptionInterface::class, $option);
    }

    public function testGetName(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertSame('foo', $option->getName());
    }

    public function testGetDescription(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertSame('Foo.', $option->getDescription());
    }

    /**
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault(bool $isRequired): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', $isRequired),
            'deprecated'
        );

        static::assertSame(!$isRequired, $option->hasDefault());
    }

    public function provideHasDefaultCases(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param mixed $default
     *
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault($default): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', false, $default),
            'deprecated'
        );

        static::assertSame($default, $option->getDefault());
    }

    public function provideGetDefaultCases(): array
    {
        return [
            ['foo'],
            [true],
        ];
    }

    public function testGetAllowedTypes(): void
    {
        $allowedTypes = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, $allowedTypes),
            'deprecated'
        );

        static::assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public function testGetAllowedValues(): void
    {
        $allowedValues = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, [], $allowedValues),
            'deprecated'
        );

        static::assertSame($allowedValues, $option->getAllowedValues());
    }

    public function testGetNormalizer(): void
    {
        $normalizer = static fn () => null;

        $decoratedOption = $this->prophesize(FixerOptionInterface::class);
        $decoratedOption->getNormalizer()->willReturn($normalizer);

        $option = new DeprecatedFixerOption(
            $decoratedOption->reveal(),
            'deprecated'
        );

        static::assertSame($normalizer, $option->getNormalizer());
    }

    public function testGetDeprecationMessage(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'Use option "bar" instead.'
        );

        static::assertSame('Use option "bar" instead.', $option->getDeprecationMessage());
    }
}
