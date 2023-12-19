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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\TypeIntersectionTransformer
 */
final class TypeIntersectionTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int|string> $expectedTokens
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP 8.1
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_TYPE_INTERSECTION,
            ]
        );
    }

    public static function provideProcessCases(): iterable
    {
        yield 'do not fix cases' => [
            '<?php
                echo 2 & 4;
                echo "aaa" & "bbb";
                echo F_OK & F_ERR;
                echo foo(F_OK & F_ERR);
                foo($A&&$b);
                foo($A&$b);
                // try {} catch (ExceptionType1 & ExceptionType2) {}
                $a = function(){};
                $x = ($y&$z);
                function foo(){}
                $a = $b&$c;
                $a &+ $b;
                const A1 = B&C;
                const B1 = D::X & C;
            ',
        ];

        if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) { // @TODO: drop condition when PHP 8.1+ is required
            yield 'ensure T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG is not modified' => [
                '<?php $a = $b&$c;',
                [
                    6 => T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG,
                ],
            ];

            yield 'do not fix, close/open' => [
                '<?php fn() => 0 ?><?php $a = FOO|BAR|BAZ&$x;',
                [
                    20 => T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG,
                ],
            ];

            yield 'do not fix, foreach' => [
                '<?php while(foo()){} $a = FOO|BAR|BAZ&$x;',
                [
                    19 => T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG,
                ],
            ];
        }

        yield 'arrow function' => [
            '<?php $a = fn(int&null $item): int&null => $item * 2;',
            [
                8 => CT::T_TYPE_INTERSECTION,
                16 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'static function' => [
            '<?php
$a = static function (A&B&int $a):int&null {};
',
            [
                11 => CT::T_TYPE_INTERSECTION,
                13 => CT::T_TYPE_INTERSECTION,
                20 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'static function with use' => [
            '<?php
$a = static function () use ($a) : int&null {};
',
            [
                21 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'function variable unions' => [
            '<?php
function Bar1(A&B&int $a) {
}
',
            [
                6 => CT::T_TYPE_INTERSECTION,
                8 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'class method variable unions' => [
            '<?php
class Foo
{
    public function Bar1(A&B&int $a) {}
    public function Bar2(A\B&\A\Z $a) {}
    public function Bar3(int $a) {}
}
',
            [
                // Bar1
                14 => CT::T_TYPE_INTERSECTION,
                16 => CT::T_TYPE_INTERSECTION,
                // Bar2
                34 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'class method return unions' => [
            '<?php
class Foo
{
    public function Bar(): A&B&int {}
}
',
            [
                17 => CT::T_TYPE_INTERSECTION,
                19 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'class attribute var + union' => [
            '<?php
class Number
{
    var int&float&null $number;
}
',
            [
                10 => CT::T_TYPE_INTERSECTION,
                12 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'class attributes visibility + unions' => [
            '<?php
class Number
{
    public array $numbers;

    /**  */
    public int&float $number1;

    // 2
    protected int&float&null $number2;

    # - foo 3
    private int&float&string&null $number3;

    /* foo 4 */
    private \Foo\Bar\A&null $number4;

    private \Foo&Bar $number5;

    private ?Bar $number6; // ? cannot be part of a union in PHP8
}
',
            [
                // number 1
                19 => CT::T_TYPE_INTERSECTION,
                // number 2
                30 => CT::T_TYPE_INTERSECTION,
                32 => CT::T_TYPE_INTERSECTION,
                // number 3
                43 => CT::T_TYPE_INTERSECTION,
                45 => CT::T_TYPE_INTERSECTION,
                47 => CT::T_TYPE_INTERSECTION,
                // number 4
                63 => CT::T_TYPE_INTERSECTION,
                // number 5
                73 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'typed static properties' => [
            '<?php
            class Foo {
                private static int & null $bar;

                private static int & float & string & null $baz;
            }',
            [
                14 => CT::T_TYPE_INTERSECTION,
                27 => CT::T_TYPE_INTERSECTION,
                31 => CT::T_TYPE_INTERSECTION,
                35 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'array as first element of types' => [
            '<?php function foo(array&bool&null $foo) {}',
            [
                6 => CT::T_TYPE_INTERSECTION,
                8 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'array as middle element of types' => [
            '<?php function foo(null&array&bool $foo) {}',
            [
                6 => CT::T_TYPE_INTERSECTION,
                8 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'array as last element of types' => [
            '<?php function foo(null&bool&array $foo) {}',
            [
                6 => CT::T_TYPE_INTERSECTION,
                8 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'multiple function parameters' => [
            '<?php function foo(A&B $x, C&D $y, E&F $z) {};',
            [
                6 => CT::T_TYPE_INTERSECTION,
                13 => CT::T_TYPE_INTERSECTION,
                20 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'function calls and function definitions' => [
            '<?php
                f1(CONST_A&CONST_B);
                function f2(A&B $x, C&D $y, E&F $z) {};
                f3(CONST_A&CONST_B);
                function f4(A&B $x, C&D $y, E&F $z) {};
                f5(CONST_A&CONST_B);
                $x = ($y&$z);
            ',
            [
                15 => CT::T_TYPE_INTERSECTION,
                22 => CT::T_TYPE_INTERSECTION,
                29 => CT::T_TYPE_INTERSECTION,
                52 => CT::T_TYPE_INTERSECTION,
                59 => CT::T_TYPE_INTERSECTION,
                66 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'static function with alternation' => [
            '<?php
$a = static function (A&B&int $a):int|null {};
',
            [
                11 => CT::T_TYPE_INTERSECTION,
                13 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'promoted properties' => [
            '<?php class Foo {
                public function __construct(
                    public readonly int&string $a,
                    protected readonly int&string $b,
                    private readonly int&string $c
                ) {}
            }',
            [
                19 => CT::T_TYPE_INTERSECTION,
                30 => CT::T_TYPE_INTERSECTION,
                41 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'callable type' => [
            '<?php
                function f1(array&callable $x) {};
                function f2(callable&array $x) {};
                function f3(string&callable $x) {};
                function f4(callable&string $x) {};
            ',
            [
                7 => CT::T_TYPE_INTERSECTION,
                22 => CT::T_TYPE_INTERSECTION,
                37 => CT::T_TYPE_INTERSECTION,
                52 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield [
            '<?php

use Psr\Log\LoggerInterface;
function f( #[Target(\'xxx\')] LoggerInterface&A $logger) {}

',
            [
                24 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield [
            '<?php

use Psr\Log\LoggerInterface;
function f( #[Target(\'a\')] #[Target(\'b\')] #[Target(\'c\')] #[Target(\'d\')] LoggerInterface&X $logger) {}

',
            [
                45 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'parameters by reference' => [
            '<?php
                f(FOO|BAR|BAZ&$x);
                function f1(FOO|BAR|BAZ&$x) {}
                function f2(FOO&BAR&BAZ&$x) {} // Intersection found
                f(FOO&BAR|BAZ&$x);
                f(FOO|BAR&BAZ&$x);
                fn(FOO&BAR&BAZ&$x) => 0; // Intersection found
                fn(FOO|BAR|BAZ&$x) => 0;
                f(FOO&BAR&BAZ&$x);
            ',
            [
                35 => CT::T_TYPE_INTERSECTION,
                37 => CT::T_TYPE_INTERSECTION,
                75 => CT::T_TYPE_INTERSECTION,
                77 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'self as type' => [
            '<?php class Foo {
                function f1(bool&self&int $x): void {}
                function f2(): self&\stdClass {}
            }',
            [
                12 => CT::T_TYPE_INTERSECTION,
                14 => CT::T_TYPE_INTERSECTION,
                34 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'static as type' => [
            '<?php class Foo {
                function f1(): static&TypeA {}
                function f2(): TypeA&static&TypeB {}
                function f3(): TypeA&static {}
            }',
            [
                15 => CT::T_TYPE_INTERSECTION,
                29 => CT::T_TYPE_INTERSECTION,
                31 => CT::T_TYPE_INTERSECTION,
                45 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'splat operator' => [
            '<?php class Foo {
                function f1(bool&int ... $x) {}
                function f2(bool&int $x, TypeA&\Bar\Baz&TypeB ...$y) {}
            }',
            [
                12 => CT::T_TYPE_INTERSECTION,
                28 => CT::T_TYPE_INTERSECTION,
                35 => CT::T_TYPE_INTERSECTION,
                40 => CT::T_TYPE_INTERSECTION,
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess82Cases
     *
     * @requires PHP 8.2
     */
    public function testProcess82(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    public static function provideProcess82Cases(): iterable
    {
        yield 'disjunctive normal form types parameter' => [
            '<?php function foo((A&B)|D $x): void {}',
            [
                7 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'disjunctive normal form types return' => [
            '<?php function foo(): (A&B)|D {}',
            [
                10 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'disjunctive normal form types parameters' => [
            '<?php function foo(
                (A&B)|C|D $x,
                A|(B&C)|D $y,
                (A&B)|(C&D) $z,
            ): void {}',
            [
                8 => CT::T_TYPE_INTERSECTION,
                23 => CT::T_TYPE_INTERSECTION,
                34 => CT::T_TYPE_INTERSECTION,
                40 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'lambda with lots of DNF parameters and some others' => [
            '<?php
$a = function(
    (X&Y)|C $a,
    $b = array(1,2),
    (\X&\Y)|C $c,
    array $d = [1,2],
    (\X&\Y)|C $e,
    $x, $y, $z, P|(H&J) $uu,
) {};

function foo (array $a = array(66,88, $d = [99,44],array()), $e = [99,44],(C&V)|G|array $f = array()){};

return new static();
',
            [
                10 => CT::T_TYPE_INTERSECTION, // $a
                34 => CT::T_TYPE_INTERSECTION, // $c
                60 => CT::T_TYPE_INTERSECTION, // $e
                83 => CT::T_TYPE_INTERSECTION, // $uu
                142 => CT::T_TYPE_INTERSECTION, // $f
            ],
        ];

        yield 'bigger set of multiple DNF properties' => [
            '<?php
class Dnf
{
    public A|(C&D) $a;
    protected (C&D)|B $b;
    private (C&D)|(E&F)|(G&H) $c;
    static (C&D)|Z $d;
    public /* */ (C&D)|X $e;

    public function foo($a, $b) {
        return
            $z|($A&$B)|(A::z&B\A::x)
            || A::b|($A&$B)
        ;
    }
}
',
            [
                13 => CT::T_TYPE_INTERSECTION,
                24 => CT::T_TYPE_INTERSECTION,
                37 => CT::T_TYPE_INTERSECTION,
                43 => CT::T_TYPE_INTERSECTION,
                49 => CT::T_TYPE_INTERSECTION,
                60 => CT::T_TYPE_INTERSECTION,
                75 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'arrow function with DNF types' => [
            '<?php
                $f1 = fn (): A|(B&C) => new Foo();
                $f2 = fn ((A&B)|C $x, A|(B&C) $y): (A&B&C)|D|(E&F) => new Bar();
            ',
            [
                16 => CT::T_TYPE_INTERSECTION,
                38 => CT::T_TYPE_INTERSECTION,
                51 => CT::T_TYPE_INTERSECTION,
                61 => CT::T_TYPE_INTERSECTION,
                63 => CT::T_TYPE_INTERSECTION,
                71 => CT::T_TYPE_INTERSECTION,
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess83Cases
     *
     * @requires PHP 8.3
     */
    public function testProcess83(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    public static function provideProcess83Cases(): iterable
    {
        yield 'typed const DNF types 1' => [
            '<?php class Foo { const (A&B)|Z Bar = 1;}',
            [
                11 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'typed const DNF types 2' => [
            '<?php class Foo { const Z|(A&B) Bar = 1;}',
            [
                13 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'typed const DNF types 3' => [
            '<?php class Foo { const Z|(A&B)|X Bar = 1;}',
            [
                13 => CT::T_TYPE_INTERSECTION,
            ],
        ];

        yield 'typed const' => [
            '<?php class Foo { const A&B Bar = 1;}',
            [
                10 => CT::T_TYPE_INTERSECTION,
            ],
        ];
    }
}
