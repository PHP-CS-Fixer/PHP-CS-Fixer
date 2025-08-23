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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AllowedValueSubsetTest extends TestCase
{
    /**
     * @param non-empty-list<string> $expected
     * @param non-empty-list<string> $input
     *
     * @dataProvider provideGetAllowedValuesAreSortedCases
     */
    public function testGetAllowedValuesAreSorted(array $expected, array $input): void
    {
        $subset = new AllowedValueSubset($input);

        self::assertSame($expected, $subset->getAllowedValues());
    }

    /**
     * @return iterable<int, array{list<string>, list<string>}>
     */
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
     * @param mixed $inputValue
     *
     * @dataProvider provideInvokeCases
     */
    public function testInvoke($inputValue, bool $expectedResult): void
    {
        $subset = new AllowedValueSubset(['foo', 'bar']);

        self::assertSame($expectedResult, $subset($inputValue));
    }

    /**
     * @return iterable<int, array{mixed, bool}>
     */
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
