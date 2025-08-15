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
 * @covers \PhpCsFixer\Tokenizer\Transformer\DisjunctiveNormalFormTypeParenthesisTransformer
 *
 * @internal
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @requires PHP 8.2
 */
final class DisjunctiveNormalFormTypeParenthesisTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens, [CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE]);
    }

    /**
     * @return iterable<string, array{string, _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield 'lambda with lots of arguments and some others' => [
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
                8 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $a
                12 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                31 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $c
                37 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                57 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $e
                63 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                81 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $uu
                85 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                140 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $f
                144 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'one line function signatures with comments' => [
            '<?php function Foo1 ((A&B)|C $x1, \C|(A&B) $x2,/* No (X&Y) ! */(A&B)|I $xxx): void {}',
            [
                6 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $x1
                10 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                20 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $x2
                24 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                29 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // $xxx
                33 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'multiple functions and arguments' => [
            '<?php
function Foo1 ((A&B)|C $x1): void {}
function Foo2 (C|(A&B) $x2): void {}
function Foo3 (C|(A&B)|D $x3): void {}
function Foo4 ((A&B)|(\C&E\B\D) $x4): void {}
function Foo5 ($x5): void {}
',
            [
                6 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // Foo1
                10 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                30 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // Foo2
                34 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                52 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // Foo3
                56 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                74 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // Foo4
                78 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                80 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                89 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'multiple functions including methods' => [
            '<?php
function Foo():M|(A&B)|J {}
$a = function(): X|(A&B) {};
interface Bar {
    function G():(A&C)|(A&B);
}
class G {
    public function X():K|(Z&Y)|P{}
}
',
            [
                9 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                13 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                31 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                35 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                53 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                57 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                59 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                63 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                84 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                88 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'multiple functions including methods 2' => [
            '<?php
function Foo():(A&B)|M {}
$a = function(): (A&B)|N {};
interface Bar {
    function G():(A&C)|I;
}
class G {
    public function X():(Z&Y)|P{}
}
',
            [
                7 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                11 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                27 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                31 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                51 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                55 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                76 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                80 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'DNF property, A|(C&D)' => [
            '<?php
class Dnf
{
    public A|(C&D) $a;
}

const E = 1;
const F = 2;

(E&F);

',
            [
                11 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                15 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'DNF property, (A&B)|C' => [
            '<?php
class Dnf
{
    public (A&B)|C $a;
}',
            [
                9 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                13 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'DNF property, two groups with string and namespace separator' => [
            '<?php
class Dnf
{
public (A\BH&Z\SAD\I)|(G\J&F\G\AK) $a;
}
',
            [
                9 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                19 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                21 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                31 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'bigger set of multiple DNF properties' => [
            '<?php
class Dnf
{
    public /* A|(C&D) */ A|(C&D) $a;
    protected (C&D)|B $b;
    private (C&D)|(E&F)|(G&H) $c;
    static (C&D)|Z $d;
    public /** (C&D)|X */ (C&D)|X $e;

    public function foo($a, $b) {
        return
            $z|($A&$B)|(A::z&B\A::x)
            || A::b|($A&$B)
        ;
    }
}
',
            [
                13 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                17 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                24 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                28 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                37 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                41 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                43 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                47 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                49 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                53 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                60 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                64 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                75 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                79 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'constructor property promotion' => [
            '<?php
class Dnf
{
    public function __construct(
        public Y|(A&B) $x,
        protected (A&B)|X $u,
        private (\A\C&B\A)|(\D\C&\E\D) $i,
    ) {}
}
',
            [
                18 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                22 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                29 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                33 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                42 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                51 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                53 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                63 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'more properties' => [
            '<?php
class Dnf
{
    public A|B|C|(E&D) $a;
    public E/**/ | X\C\B | /** */  \C|(E1&D1) $v;
    public F | B | C | (D&Y) | E | (F&O) $a;
    static G|B|C|(E77&D88) $XYZ;
    static public (H&S)|B $f;
    public static I|(P&S11) $f2;
}

static $a = 1;
return new static();
',
            [
                15 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // E&D
                19 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                44 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // E1&D1
                48 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                67 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // D&Y
                71 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                79 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // F&O
                83 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                96 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // E77&D88
                100 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                109 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // P&S
                113 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                126 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, // f2
                130 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'arrow functions' => [
            '<?php
                $f1 = fn (): A|(B&C) => new Foo();
                $f2 = fn ((A&B)|C $x, A|(B&C) $y): (A&B&C)|D|(E&F) => new Bar();
            ',
            [
                14 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                18 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                36 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                40 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                49 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                53 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                59 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                65 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                69 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                73 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];

        yield 'invokable class' => [
            '<?php
                class A
                {
                    public function __invoke((Foo&Bar)|string $a): string
                        {
                            return "A";
                        }
                    }

                return (new A())(new class implements Foo, Bar {});
            ',
            [
                14 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                18 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];
    }

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess83Cases
     *
     * @requires PHP 8.3
     */
    public function testProcess83(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    /**
     * @return iterable<string, array{string, _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcess83Cases(): iterable
    {
        yield 'typed const' => [
            '<?php
                class Foo {
                    const A|(B&C) Bar = 1;
                    const (B&C)|A Bar = 2;
                    const D|(B&C)|A Bar = 3;
                }',
            [
                12 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                16 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                27 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                31 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
                46 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
                50 => CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
            ],
        ];
    }
}
