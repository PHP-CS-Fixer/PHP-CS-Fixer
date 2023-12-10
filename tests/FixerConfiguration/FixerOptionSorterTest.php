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

use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionSorter;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerOptionSorter
 */
final class FixerOptionSorterTest extends TestCase
{
    public function testSortAcceptsEmptyArray(): void
    {
        $unsorted = [];

        $sorter = new FixerOptionSorter();

        $expected = [];

        self::assertSame($expected, $sorter->sort($unsorted));
    }

    public function testSortSortsArrayOfOptionsByName(): void
    {
        $fooOption = new FixerOption('foo', 'Bar.');
        $bazOption = new FixerOption('baz', 'Qux.');

        $unsorted = [
            $fooOption,
            $bazOption,
        ];

        $sorter = new FixerOptionSorter();

        $expected = [
            $bazOption,
            $fooOption,
        ];

        self::assertSame($expected, $sorter->sort($unsorted));
    }

    public function testSortAcceptsEmptyIterable(): void
    {
        $unsorted = new \ArrayIterator();

        $sorter = new FixerOptionSorter();

        $expected = [];

        self::assertSame($expected, $sorter->sort($unsorted));
    }

    public function testSortSortsIterableOfOptionsByName(): void
    {
        $fooOption = new FixerOption('foo', 'Bar.');
        $bazOption = new FixerOption('baz', 'Qux.');

        $unsorted = new \ArrayIterator([
            $fooOption,
            $bazOption,
        ]);

        $sorter = new FixerOptionSorter();

        $expected = [
            $bazOption,
            $fooOption,
        ];

        self::assertSame($expected, $sorter->sort($unsorted));
    }
}
