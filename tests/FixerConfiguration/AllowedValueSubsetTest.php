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

use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AllowedValueSubset
 */
final class AllowedValueSubsetTest extends TestCase
{
    public static function provideGetAllowedValuesAreSortedCases(): iterable
    {
        yield [
            ['bar', 'foo'],
            ['foo', 'bar'],
        ];

        yield [
            ['bar', 'Foo'],
            ['Foo', 'bar'],
        ];
    }

    /**
     * @param list<string> $expected
     * @param list<string> $input
     *
     * @dataProvider provideGetAllowedValuesAreSortedCases
     */
    public function testGetAllowedValuesAreSorted(array $expected, array $input): void
    {
        $subset = new AllowedValueSubset($input);

        self::assertSame($expected, $subset->getAllowedValues());
    }

    /**
     * @param mixed $inputValue
     *
     * @dataProvider provideInvokeCases
     */
    public function testInvoke($inputValue, bool $expectedResult): void
    {
        $subset = new AllowedValueSubset(['foo', 'bar']);

        self::assertSame($expectedResult, $subset($inputValue));
    }

    public static function provideInvokeCases(): iterable
    {
        yield [
            ['foo', 'bar'],
            true,
        ];

        yield [
            ['bar', 'foo'],
            true,
        ];

        yield [
            ['foo'],
            true,
        ];

        yield [
            ['bar'],
            true,
        ];

        yield [
            [],
            true,
        ];

        yield [
            ['foo', 'bar', 'baz'],
            false,
        ];

        yield [
            ['baz'],
            false,
        ];

        yield [
            1,
            false,
        ];

        yield [
            1.2,
            false,
        ];

        yield [
            'foo',
            false,
        ];

        yield [
            new \stdClass(),
            false,
        ];

        yield [
            true,
            false,
        ];

        yield [
            null,
            false,
        ];
    }

    public function testGetAllowedValues(): void
    {
        $values = ['bar', 'foo'];

        $subset = new AllowedValueSubset($values);

        self::assertSame($values, $subset->getAllowedValues());
    }
}
