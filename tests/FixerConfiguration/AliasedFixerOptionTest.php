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

use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AliasedFixerOption
 */
final class AliasedFixerOptionTest extends TestCase
{
    /**
     * @dataProvider provideGetNameCases
     */
    public function testGetName(string $name): void
    {
        $option = new AliasedFixerOption(new FixerOption($name, 'Bar.'), 'baz');

        static::assertSame($name, $option->getName());
    }

    public function provideGetNameCases(): array
    {
        return [
            ['foo'],
            ['bar'],
        ];
    }

    /**
     * @dataProvider provideGetDescriptionCases
     */
    public function testGetDescription(string $description): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', $description), 'baz');

        static::assertSame($description, $option->getDescription());
    }

    public function provideGetDescriptionCases(): array
    {
        return [
            ['Foo.'],
            ['Bar.'],
        ];
    }

    /**
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault(bool $hasDefault, AliasedFixerOption $input): void
    {
        static::assertSame($hasDefault, $input->hasDefault());
    }

    public function provideHasDefaultCases(): array
    {
        return [
            [
                false,
                new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz'),
            ],
            [
                true,
                new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, 'baz'), 'baz'),
            ],
        ];
    }

    /**
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault(string $default): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, $default), 'baz');

        static::assertSame($default, $option->getDefault());
    }

    public function provideGetDefaultCases(): array
    {
        return [
            ['baz'],
            ['foo'],
        ];
    }

    public function testGetUndefinedDefault(): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No default value defined.');
        $option->getDefault();
    }

    /**
     * @param null|list<string> $allowedTypes
     *
     * @dataProvider provideGetAllowedTypesCases
     */
    public function testGetAllowedTypes(?array $allowedTypes): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, $allowedTypes), 'baz');

        static::assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public function provideGetAllowedTypesCases(): array
    {
        return [
            [null],
            [['bool']],
            [['bool', 'string']],
        ];
    }

    /**
     * @param list<(callable(mixed): bool)|null|scalar>|null $allowedValues
     *
     * @dataProvider provideGetAllowedValuesCases
     */
    public function testGetAllowedValues(?array $allowedValues): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, $allowedValues), 'baz');

        static::assertSame($allowedValues, $option->getAllowedValues());
    }

    public function provideGetAllowedValuesCases(): array
    {
        return [
            [null],
            [['baz']],
            [['baz', 'qux']],
        ];
    }

    public function testGetAllowedValuesClosure(): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, [static fn () => true]), 'baz');
        $allowedTypes = $option->getAllowedValues();
        static::assertIsArray($allowedTypes);
        static::assertCount(1, $allowedTypes);
        static::assertArrayHasKey(0, $allowedTypes);
        static::assertInstanceOf(\Closure::class, $allowedTypes[0]);
    }

    public function testGetNormalizers(): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz');
        static::assertNull($option->getNormalizer());

        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, null, static fn () => null), 'baz');
        static::assertInstanceOf(\Closure::class, $option->getNormalizer());
    }

    /**
     * @dataProvider provideGetAliasCases
     */
    public function testGetAlias(string $alias): void
    {
        $options = new AliasedFixerOption(new FixerOption('foo', 'Bar', true, null, null, null, null), $alias);

        static::assertSame($alias, $options->getAlias());
    }

    public function provideGetAliasCases(): array
    {
        return [
            ['bar'],
            ['baz'],
        ];
    }

    public function testRequiredWithDefaultValue(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Required options cannot have a default value.');

        new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, false), 'baz');
    }
}
