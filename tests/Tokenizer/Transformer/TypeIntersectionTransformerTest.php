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
     * @param array<int, int> $expectedTokens
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

    public function provideProcessCases(): iterable
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
            ',
        ];

        if (\PHP_VERSION_ID >= 80100) {
            yield 'ensure T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG is not modified' => [
                '<?php $a = $b&$c;',
                [
                    6 => T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG,
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
    }
}
