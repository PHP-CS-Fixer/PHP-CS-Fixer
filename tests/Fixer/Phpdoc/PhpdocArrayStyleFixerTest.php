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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocArrayStyleFixer
 */
final class PhpdocArrayStyleFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array{strategy?: string} $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: null|string, 2?: array{strategy: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php /** @tagNotSupportingTypes string[] */'];

        yield ['<?php /** @var $variableWithoutType */'];

        yield [
            '<?php /** @var int[] */',
            null,
            ['strategy' => 'array_to_list'],
        ];

        yield [
            '<?php /** @var array<int> */',
            '<?php /** @var int[] */',
            ['strategy' => 'brackets_to_array'],
        ];

        yield [
            '<?php /** @var list<int> */',
            '<?php /** @var int[] */',
            ['strategy' => 'brackets_to_array_to_list'],
        ];

        yield [
            '<?php /** @param array<array<array<array<int>>>> $x */',
            '<?php /** @param int[][][][] $x */',
        ];

        yield [
            '<?php /** @return iterable<array<int>> */',
            '<?php /** @return iterable<int[]> */',
        ];

        yield [
            '<?php /** @var array<Foo\Bar> */',
            '<?php /** @var Foo\Bar[] */',
        ];

        yield [
            '<?php /** @var array<Foo_Bar> */',
            '<?php /** @var Foo_Bar[] */',
        ];

        yield [
            '<?php /** @var array<bool>|array<float>|array<int>|array<string> */',
            '<?php /** @var array<bool>|float[]|array<int>|string[] */',
        ];

        yield [
            <<<'PHP'
                <?php
                /** @return array<int> */
                /*  @return int[] */
                PHP,
            <<<'PHP'
                <?php
                /** @return int[] */
                /*  @return int[] */
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                /** @var array<int, string> */
                /** @var array<int, array<string, bool>> */
                /** @var array<int, array{string, string, string}> */
                /** @var array{string, string, string} */
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                /**
                 * @param array{foo?: array<int>} $foo
                 * @param callable(array<int>): array<int> $bar
                 */
                PHP,
            <<<'PHP'
                <?php
                /**
                 * @param array{foo?: int[]} $foo
                 * @param callable(int[]): int[] $bar
                 */
                PHP,
        ];

        yield [
            '<?php /** @var array<int> */',
            null,
            ['strategy' => 'brackets_to_array'],
        ];

        yield [
            <<<'PHP'
                <?php
                /** @var list<Foo> */
                /** @var list<Foo> */
                /** @var list<Foo> */
                /** @var array<int, Foo> */
                /** @var array{Foo} */
                /** @var array{int, Foo} */
                PHP,
            <<<'PHP'
                <?php
                /** @var Foo[] */
                /** @var array<Foo> */
                /** @var list<Foo> */
                /** @var array<int, Foo> */
                /** @var array{Foo} */
                /** @var array{int, Foo} */
                PHP,
            ['strategy' => 'brackets_to_array_to_list'],
        ];

        yield [
            '<?php /** @var list<list<list<list<int>>>> */',
            '<?php /** @var int[][][][] */',
            ['strategy' => 'brackets_to_array_to_list'],
        ];
    }
}
