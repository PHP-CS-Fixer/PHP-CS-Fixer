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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocArrayTypeFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocArrayTypeFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpdocArrayTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            <<<'PHP'
                <?php
                /** @tagNotSupportingTypes string[] */
                /** @var array{'a', 'b[]', 'c'} */
                /** @var 'x|T1[][]|y' */
                /** @var "x|T2[][]|y" */
                PHP,
        ];

        yield [
            '<?php /** @var array<int> */',
            '<?php /** @var int[] */',
        ];

        yield [
            '<?php /** @param array<array<array<array<int>>>> $x */',
            '<?php /** @param int[][][][] $x */',
        ];

        yield [
            '<?php /** @param array<array<array<array<int>>>> $x */',
            '<?php /** @param int    [][  ][]  [] $x */',
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
            <<<'PHP'
                <?php
                /**
                 * @var array<array{'a'}>
                 * @var \Closure(iterable<\DateTime>): array<void>
                 * @var array<Yes>|'No[][]'|array<array<Yes>>|'No[]'
                 */
                PHP,
            <<<'PHP'
                <?php
                /**
                 * @var array{'a'}[]
                 * @var \Closure(iterable<\DateTime>): void[]
                 * @var Yes[]|'No[][]'|Yes[][]|'No[]'
                 */
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                /**
                 * @param ?array<type> $a
                 * @param ?array<array<type>> $b
                 * @param ?'literal[]' $c
                 */
                PHP,
            <<<'PHP'
                <?php
                /**
                 * @param ?type[] $a
                 * @param ?type[][] $b
                 * @param ?'literal[]' $c
                 */
                PHP,
        ];

        yield [
            '<?php /** @var array<Foo|Bar> */',
            '<?php /** @var (Foo|Bar)[] */',
        ];

        yield [
            '<?php /** @var (Foo&Bar)|array<Baz> */',
            '<?php /** @var (Foo&Bar)|Baz[] */',
        ];

        $expected = $input = 'string';
        for ($i = 0; $i < 32; ++$i) {
            $expected = 'array<'.$expected.'>';
            $input .= '[]';
        }

        yield [
            \sprintf('<?php /** @var %s */', $expected),
            \sprintf('<?php /** @var %s */', $input),
        ];

        yield [
            '<?php /** @return array<Foo<covariant TEntity>> */',
            '<?php /** @return Foo<covariant TEntity>[] */',
        ];
    }
}
