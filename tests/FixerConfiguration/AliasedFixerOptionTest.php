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

        self::assertSame($name, $option->getName());
    }

    public static function provideGetNameCases(): iterable
    {
        yield ['foo'];

        yield ['bar'];
    }

    /**
     * @dataProvider provideGetDescriptionCases
     */
    public function testGetDescription(string $description): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', $description), 'baz');

        self::assertSame($description, $option->getDescription());
    }

    public static function provideGetDescriptionCases(): iterable
    {
        yield ['Foo.'];

        yield ['Bar.'];
    }

    /**
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault(bool $hasDefault, AliasedFixerOption $input): void
    {
        self::assertSame($hasDefault, $input->hasDefault());
    }

    public static function provideHasDefaultCases(): iterable
    {
        yield [
            false,
            new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz'),
        ];

        yield [
            true,
            new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, 'baz'), 'baz'),
        ];
    }

    /**
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault(string $default): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, $default), 'baz');

        self::assertSame($default, $option->getDefault());
    }

    public static function provideGetDefaultCases(): iterable
    {
        yield ['baz'];

        yield ['foo'];
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

        self::assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public static function provideGetAllowedTypesCases(): iterable
    {
        yield [null];

        yield [['bool']];

        yield [['bool', 'string']];
    }

    /**
     * @param null|list<null|(callable(mixed): bool)|scalar> $allowedValues
     *
     * @dataProvider provideGetAllowedValuesCases
     */
    public function testGetAllowedValues(?array $allowedValues): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, $allowedValues), 'baz');

        self::assertSame($allowedValues, $option->getAllowedValues());
    }

    public static function provideGetAllowedValuesCases(): iterable
    {
        yield [null];

        yield [['baz']];

        yield [['baz', 'qux']];
    }

    public function testGetAllowedValuesClosure(): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, [static fn () => true]), 'baz');
        $allowedTypes = $option->getAllowedValues();
        self::assertIsArray($allowedTypes);
        self::assertCount(1, $allowedTypes);
        self::assertArrayHasKey(0, $allowedTypes);
        self::assertInstanceOf(\Closure::class, $allowedTypes[0]);
    }

    public function testGetNormalizers(): void
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz');
        self::assertNull($option->getNormalizer());

        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, null, static fn () => null), 'baz');
        self::assertInstanceOf(\Closure::class, $option->getNormalizer());
    }

    /**
     * @dataProvider provideGetAliasCases
     */
    public function testGetAlias(string $alias): void
    {
        $options = new AliasedFixerOption(new FixerOption('foo', 'Bar', true, null, null, null, null), $alias);

        self::assertSame($alias, $options->getAlias());
    }

    public static function provideGetAliasCases(): iterable
    {
        yield ['bar'];

        yield ['baz'];
    }

    public function testRequiredWithDefaultValue(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Required options cannot have a default value.');

        new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, false), 'baz');
    }
}
