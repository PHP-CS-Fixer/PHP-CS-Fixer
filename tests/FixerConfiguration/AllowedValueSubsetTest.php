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
    public function testConstructor(): void
    {
        static::assertIsCallable(new AllowedValueSubset(['foo', 'bar']));
    }

    public function provideGetAllowedValuesAreSortedCases(): iterable
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

        static::assertSame($expected, $subset->getAllowedValues());
    }

    /**
     * @param mixed $inputValue
     *
     * @dataProvider provideInvokeCases
     */
    public function testInvoke($inputValue, bool $expectedResult): void
    {
        $subset = new AllowedValueSubset(['foo', 'bar']);

        static::assertSame($expectedResult, $subset($inputValue));
    }

    public function provideInvokeCases(): array
    {
        return [
            [
                ['foo', 'bar'],
                true,
            ],
            [
                ['bar', 'foo'],
                true,
            ],
            [
                ['foo'],
                true,
            ],
            [
                ['bar'],
                true,
            ],
            [
                [],
                true,
            ],
            [
                ['foo', 'bar', 'baz'],
                false,
            ],
            [
                ['baz'],
                false,
            ],
            [
                1,
                false,
            ],
            [
                1.2,
                false,
            ],
            [
                'foo',
                false,
            ],
            [
                new \stdClass(),
                false,
            ],
            [
                true,
                false,
            ],
            [
                null,
                false,
            ],
        ];
    }

    public function testGetAllowedValues(): void
    {
        $values = ['bar', 'foo'];

        $subset = new AllowedValueSubset($values);

        static::assertSame($values, $subset->getAllowedValues());
    }
}
