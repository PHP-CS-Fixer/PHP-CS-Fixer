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
 * @requires PHP 8.2
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\DisjunctiveNormalFormTypeBracesTransformer
 */
final class DisjunctiveNormalFormTypeBracesTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessFunctionReturnTypeCases
     * @dataProvider provideProcessPropertyCases
     * @dataProvider provideProcessFunctionArgumentTypeCases
     */
    public function testProcess(array $expectedTokens, string $source): void
    {
        $this->doTest($source, $expectedTokens, [CT::T_DNF_TYPE_PARENTHESIS_OPEN, CT::T_DNF_TYPE_PARENTHESIS_CLOSE]);
    }

    public function provideProcessFunctionArgumentTypeCases(): iterable
    {
        yield 'lambda with lots of arguments and some others' => [
            [
                8 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $a
                12 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                31 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $c
                37 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                57 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $e
                63 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                81 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $uu
                85 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                140 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $f
                144 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];

        yield 'one line function signatures with comments' => [
            [
                6 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $x1
                10 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                20 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $x2
                24 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                29 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // $xxx
                33 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
            '<?php function Foo1 ((A&B)|C $x1, \C|(A&B) $x2,/**/(A&B)|I $xxx): void {}',
        ];

        yield 'multiple functions and arguments' => [
            [
                6 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // Foo1
                10 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                30 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // Foo2
                34 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                52 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // Foo3
                56 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                74 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // Foo4
                78 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                80 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                89 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
            '<?php
function Foo1 ((A&B)|C $x1): void {}
function Foo2 (C|(A&B) $x2): void {}
function Foo3 (C|(A&B)|D $x3): void {}
function Foo4 ((A&B)|(\C&E\B\D) $x4): void {}
function Foo5 ($x5): void {}
',
        ];
    }

    public function provideProcessFunctionReturnTypeCases(): iterable
    {
        yield 'multiple functions including methods' => [
            [
                9 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                13 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                31 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                35 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                53 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                57 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                59 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                63 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                84 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                88 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];

        yield 'multiple functions including methods 2' => [
            [
                7 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                11 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                27 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                31 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                51 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                55 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                76 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                80 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];
    }

    public function provideProcessPropertyCases(): iterable
    {
        yield 'DNF property, A|(C&D)' => [
            [
                11 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                15 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
            '<?php
class Dnf
{
    public A|(C&D) $a;
}

const E = 1;
const F = 2;

(E&F);

',
        ];

        yield 'DNF property, (E&F)|G' => [
            [
                11 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                15 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
            '<?php
class Dnf
{
    public A|(C&D) $a;
}',
        ];

        yield 'DNF property, two groups with string and namespace separator' => [
            [
                9 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                19 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                21 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                31 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
            '<?php
class Dnf
{
public (A\BH&Z\SAD\I)|(G\J&F\G\AK) $a;
}
',
        ];

        yield 'bigger set of multiple DNF properties' => [
            [
                11 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                15 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                22 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                26 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                35 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                39 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                41 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                45 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                47 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                51 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                58 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                62 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                73 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                77 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];

        yield 'constructor promotion' => [
            [
                18 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                22 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                29 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                33 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                42 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                51 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                53 => CT::T_DNF_TYPE_PARENTHESIS_OPEN,
                63 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];

        yield 'more properties' => [
            [
                15 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // E&D
                19 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                44 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // E1&D1
                48 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                67 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // D&Y
                71 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                79 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // F&O
                83 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                96 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // E77&D88
                100 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                109 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // P&S
                113 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
                126 => CT::T_DNF_TYPE_PARENTHESIS_OPEN, // f2
                130 => CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
            ],
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
        ];
    }
}
