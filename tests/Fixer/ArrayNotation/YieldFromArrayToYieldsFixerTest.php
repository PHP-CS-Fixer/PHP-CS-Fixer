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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\YieldFromArrayToYieldsFixer
 */
final class YieldFromArrayToYieldsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php function f() { yield from foo(); }',
        ];

        yield [
            '<?php function f() {  yield 1; yield 2; yield 3; }',
            '<?php function f() { yield from [1, 2, 3]; }',
        ];

        yield [
            '<?php function f() {  yield 11; yield 22; yield 33; }',
            '<?php function f() { yield from array(11, 22, 33); }',
        ];

        yield [
            '<?php function f() {    yield 11; yield 22; yield 33; }',
            '<?php function f() { yield from array  (11, 22, 33); }',
        ];

        yield [
            '<?php function f() {  /* ugly comment */yield 11; yield 22; yield 33; }',
            '<?php function f() { yield from array/* ugly comment */(11, 22, 33); }',
        ];

        yield [
            '<?php function f() {  /** ugly doc */yield 11; yield 22; yield 33; }',
            '<?php function f() { yield from array/** ugly doc */(11, 22, 33); }',
        ];

        yield [
            '<?php function f() {  yield 111; yield 222; yield 333; }',
            '<?php function f() { yield from [111, 222, 333,]; }',
        ];

        yield [
            '<?php function f() {
                 '.'
                    yield [1, 2];
                    yield [3, 4];
                '.'
            }',
            '<?php function f() {
                yield from [
                    [1, 2],
                    [3, 4],
                ];
            }',
        ];

        yield [
            '<?php function f() {
                 '.'
                    yield array(1, 2);
                    yield array(3, 4);
                '.'
            }',
            '<?php function f() {
                yield from [
                    array(1, 2),
                    array(3, 4),
                ];
            }',
        ];

        yield [
            '<?php function f() {
                 '.'
                    yield 1;
                    yield 2;
                    yield 3;
                '.'
            }',
            '<?php function f() {
                yield from [
                    1,
                    2,
                    3,
                ];
            }',
        ];

        yield [
            '<?php function f() {
                 '.'
                    // uno
                    yield 1;
                    // dos
                    yield 2;
                    // tres
                    yield 3;
                '.'
            }',
            '<?php function f() {
                yield from [
                    // uno
                    1,
                    // dos
                    2,
                    // tres
                    3,
                ];
            }',
        ];

        yield [
            '<?php function f() {
                 '.'
                    yield random_key() => true;
                    yield "foo" => foo(1, 2);
                    yield "bar" => function ($x, $y) { return max($x, $y); };
                    yield "baz" => function () { yield [1, 2]; };
                '.'
            }',
            '<?php function f() {
                yield from [
                    random_key() => true,
                    "foo" => foo(1, 2),
                    "bar" => function ($x, $y) { return max($x, $y); },
                    "baz" => function () { yield [1, 2]; },
                ];
            }',
        ];

        yield [
            '<?php
                function f1() {  yield 0; yield 1; yield 2; }
                function f2() {  yield 3; yield 4; yield 5; }
                function f3() {  yield 6; yield 7; yield 8; }
            ',
            '<?php
                function f1() { yield from [0, 1, 2]; }
                function f2() { yield from [3, 4, 5]; }
                function f3() { yield from [6, 7, 8]; }
            ',
        ];

        yield [
            '<?php
                function f1() {  yield 0; yield 1; }
                function f2() {  yield 2; yield 3; }
                function f3() {  yield 4; yield 5; }
                function f4() {  yield 6; yield 7; }
                function f5() {  yield 8; yield 9; }
            ',
            '<?php
                function f1() { yield from array(0, 1); }
                function f2() { yield from [2, 3]; }
                function f3() { yield from array(4, 5); }
                function f4() { yield from [6, 7]; }
                function f5() { yield from array(8, 9); }
            ',
        ];

        yield [
            '<?php
                function foo() {
                    return [
                        1,
                        yield from [2, 3],
                        4,
                    ];
                }
            ',
        ];

        yield [
            '<?php
                function foo() {
                    yield from [
                        "this element is regular string",
                        yield from ["here", "are", "nested", "strings"],
                        "next elements will be an arrow function reference",
                        fn() => [yield 1, yield from [2, 3]],
                        fn() => [yield from [1, 2], yield 3],
                        fn() => [yield from array(1, 2), yield 3]
                    ];
                }
            ',
        ];

        yield [
            '<?php function foo() {
                 yield 0; yield 1;
                 yield 2; yield 3;
                yield from [4, yield from [5, 6], 7];
                 yield 8; yield 9;
            }',
            '<?php function foo() {
                yield from [0, 1];
                yield from [2, 3];
                yield from [4, yield from [5, 6], 7];
                yield from [8, 9];
            }',
        ];

        yield [
            '<?php function foo()
            {
                foreach ([] as $x) {}
                 yield 1; yield 2; yield 3;
            }',
            '<?php function foo()
            {
                foreach ([] as $x) {}
                yield from [1, 2, 3];
            }',
        ];

        yield 'skip empty arrays' => [
            '<?php
            function foo1()
            {
                yield from [/*empty*/ ];
            }
            function foo2()
            {
                yield from [
                    // Inline comment,
                    # and another one
                ];
            }
            function foo3()
            {
                yield from array(/*empty*/ );
            }
            function bar()
            {
                yield from [];
                yield from array();
            }
            function baz()
            {
                yield from [];
                 yield 1; yield 2;
                yield from [];
            }',
            '<?php
            function foo1()
            {
                yield from [/*empty*/ ];
            }
            function foo2()
            {
                yield from [
                    // Inline comment,
                    # and another one
                ];
            }
            function foo3()
            {
                yield from array(/*empty*/ );
            }
            function bar()
            {
                yield from [];
                yield from array();
            }
            function baz()
            {
                yield from [];
                yield from [1, 2];
                yield from [];
            }',
        ];
    }
}
