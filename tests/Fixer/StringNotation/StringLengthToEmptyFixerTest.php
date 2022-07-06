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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\StringLengthToEmptyFixer
 */
final class StringLengthToEmptyFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): iterable
    {
        yield [
            '<?php $a = \'\' === $b;',
            '<?php $a = 0 === strlen($b);',
        ];

        yield 'casing' => [
            '<?php $a1 = \'\' === $b ?>',
            '<?php $a1 = 0 === \STRlen($b) ?>',
        ];

        yield 'nested' => [
            '<?php $a2 = \'\' === foo(\'\' === foo(\'\' === foo(\'\' === foo(\'\' === foo(\'\' === foo(\'\' === $b))))));',
            '<?php $a2 = 0 === strlen(foo(0 === strlen(foo(0 === strlen(foo(0 === strlen(foo(0 === strlen(foo(0 === strlen(foo(0 === strlen($b)))))))))))));',
        ];

        yield [
            '<?php $a3 = \'\' !== $b;',
            '<?php $a3 = 0 !== strlen($b);',
        ];

        yield [
            '<?php $a4 = 0 <= strlen($b);',
        ];

        yield [
            '<?php $a5 = \'\' === $b;',
            '<?php $a5 = 0 >= strlen($b);',
        ];

        yield [
            '<?php $a6 = \'\' !== $b;',
            '<?php $a6 = 0 < strlen($b);',
        ];

        yield [
            '<?php $a7 = 0 > strlen($b);',
        ];

        yield [
            '<?php $a8 = 1 === strlen($b);',
        ];

        yield [
            '<?php $a9 = 1 !== strlen($b);',
        ];

        yield [
            '<?php $a10 = \'\' !== $b;',
            '<?php $a10 = 1 <= strlen($b);',
        ];

        yield [
            '<?php $a11 = 1 >= strlen($b);',
        ];

        yield [
            '<?php $a12 = 1 < strlen($b);',
        ];

        yield [
            '<?php $a13 = \'\' === $b;',
            '<?php $a13 = 1 > strlen($b);',
        ];

        yield [
            '<?php $a14 = $b === \'\';',
            '<?php $a14 = strlen($b) === 0;',
        ];

        yield [
            '<?php $a15 = $b !== \'\';',
            '<?php $a15 = strlen($b) !== 0;',
        ];

        yield [
            '<?php $a16 = $b === \'\';',
            '<?php $a16 = strlen($b) <= 0;',
        ];

        yield [
            '<?php $a17 = strlen($b) >= 0;',
        ];

        yield [
            '<?php $a18 = strlen($b) < 0;',
        ];

        yield [
            '<?php $a19 = $b !== \'\';',
            '<?php $a19 = strlen($b) > 0;',
        ];

        yield [
            '<?php $a20 = strlen($b) === 1;',
        ];

        yield [
            '<?php $a21 = strlen($b) !== 1;',
        ];

        yield [
            '<?php $a22 = strlen($b) <= 1;',
        ];

        yield [
            '<?php $a23 = $b !== \'\';',
            '<?php $a23 = strlen($b) >= 1;',
        ];

        yield [
            '<?php $a24 = $b === \'\';',
            '<?php $a24 = strlen($b) < 1;',
        ];

        yield [
            '<?php $a25 = strlen($b) > 1;',
        ];

        yield [
            '<?php $e = 0 === foo() ? -1 : \'\' === $a;',
            '<?php $e = 0 === foo() ? -1 : 0 === strlen($a);',
        ];

        yield [
            '<?php $x = /* 1 */ $b /* 2 */ ->a !== \'\';',
            '<?php $x = strlen(/* 1 */ $b /* 2 */ ->a) >= 1;',
        ];

        yield [
            '<?php $y = $b[0] === \'\';',
            '<?php $y = strlen($b[0]) < 1;',
        ];

        yield [
            '<?php $y1 = $b[0]->$a[$a++](1) /* 1 */  === \'\';',
            '<?php $y1 = strlen($b[0]->$a[$a++](1) /* 1 */ ) < 1;',
        ];

        yield [
            '<?php $z = \'\' === $b[1]->foo(++$i, static function () use ($z){ return $z + 1;});',
            '<?php $z = 0 === strlen($b[1]->foo(++$i, static function () use ($z){ return $z + 1;}));',
        ];

        yield [
            '<?php if ((string) $node !== \'\') { echo 1; }',
            '<?php if (\strlen((string) $node) > 0) { echo 1; }',
        ];

        yield 'do not fix' => [
            '<?php
//-----------------------------------
// operator precedence

$a01 = 0 === strlen($b) ** $c;
$a03 = 0 === strlen($b) % $c;
$a04 = 0 === strlen($b) / $c;
$a05 = 0 === strlen($b) * $c;
$a06 = 0 === strlen($b) + $c;
$a07 = 0 === strlen($b) - $c;
$a08 = 0 === strlen($b) . $c;
$a09 = 0 === strlen($b) >> $c;
$a10 = 0 === strlen($b) << $c;

$a01n = strlen($b) === 0 ** $c;
$a03n = strlen($b) === 0 % $c;
$a04n = strlen($b) === 0 / $c;
$a05n = strlen($b) === 0 * $c;
$a06n = strlen($b) === 0 + $c;
$a07n = strlen($b) === 0 - $c;
$a08n = strlen($b) === 0 . $c;
$a09n = strlen($b) === 0 >> $c;
$a10n = strlen($b) === 0 << $c;

$b = "a";

$c = 0 === strlen($b) - 1;
var_dump($c);

$c = "" === $b - 1;
var_dump($c);

//-----------------------------------
// type juggle

$d = false;

$e = 0 === strlen($d) ? -1 : 0;
var_dump($e);

$e = "" === $d ? -1 : 0;
var_dump($e);

//-----------------------------------
// wrong argument count

$f = strlen(1,2);
$g = \strlen(1,2,3);

//-----------------------------------
// others

$h = 0 === (string) strlen($b);
$i = 0 === @strlen($b);
$j = 0 === !strlen($b);

$jj = 2 === strlen($b);
$jk = __DIR__ === strlen($b);
$jl = \'X\' !== strlen($b);

$jj = strlen($b) === 2;
$jk = strlen($b) === __DIR__;
$jl = strlen($b) !== \'X\';

//-----------------------------------
// not global calls

$k = 0 === $a->strlen($b);
$l = 0 === Foo::strlen($b);

//-----------------------------------
// comments

// $a = 0 === strlen($b);
# $a = 0 === strlen($b);
/* $a = 0 === strlen($b); */
/** $a = 0 === strlen($b); */
',
        ];

        // cases where `(` and `)` must be kept

        yield [
            '<?php $e = ($a = trim($b)) !== \'\';',
            '<?php $e = \strlen($a = trim($b)) > 0;',
        ];

        yield [
            '<?php if (\'\' === ($value = foo())) { echo 2; }',
            '<?php if (0 === \strlen($value = foo())) { echo 2; }',
        ];

        yield [
            '<?php
                $a02 = 0 === strlen($b) instanceof stdClass;
                $a02n = strlen($b) === 0 instanceof stdClass;',
        ];
    }
}
