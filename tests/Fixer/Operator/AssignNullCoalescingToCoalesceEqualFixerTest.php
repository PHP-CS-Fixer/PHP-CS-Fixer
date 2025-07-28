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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractShortOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\AssignNullCoalescingToCoalesceEqualFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\AssignNullCoalescingToCoalesceEqualFixer>
 */
final class AssignNullCoalescingToCoalesceEqualFixerTest extends AbstractFixerTestCase
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
        yield 'simple' => [
            '<?php $a ??= 1;',
            '<?php $a = $a ?? 1;',
        ];

        yield 'minimal' => [
            '<?php $a ??= 1;',
            '<?php $a=$a??1;',
        ];

        yield 'simple array' => [
            '<?php $a[1] ??= 1;',
            '<?php $a[1] = $a[1] ?? 1;',
        ];

        yield 'simple array [0]' => [
            '<?php $a[1][0] ??= 1;',
            '<?php $a[1][0] = $a[1][0] ?? 1;',
        ];

        yield 'simple array ([0])' => [
            '<?php $a[1][0] ??= 1;',
            '<?php $a[1][0] = ($a[1][0]) ?? 1;',
        ];

        yield 'simple array, comment' => [
            '<?php $a[1] /* 1 */ ??= /* 2 */ /* 3 */ /* 4 */ /* 5 */ 1;',
            '<?php $a[1]/* 1 */ = /* 2 */ $a[1/* 3 */] /* 4 */ ??/* 5 */ 1;',
        ];

        yield 'simple in call' => [
            '<?php a(1, $a ??= 1);',
            '<?php a(1, $a = $a ?? 1);',
        ];

        yield [
            '<?php \A\B::$foo ??= 1;',
            '<?php \A\B::$foo = \A\B::$foo ?? 1;',
        ];

        yield 'same' => [
            '<?php $a ??= 1;',
            '<?php $a = ($a) ?? 1;',
        ];

        yield 'object' => [
            '<?php $a->b ??= 1;',
            '<?php $a->b = $a->b ?? 1;',
        ];

        yield 'object II' => [
            '<?php $a->b[0]->{1} ??= 1;',
            '<?php $a->b[0]->{1} = $a->b[0]->{1} ?? 1;',
        ];

        yield 'simple, before ;' => [
            '<?php ; $a ??= 1;',
            '<?php ; $a = $a ?? 1;',
        ];

        yield 'simple, before {' => [
            '<?php { $a ??= 1; }',
            '<?php { $a = $a ?? 1; }',
        ];

        yield 'simple, before }' => [
            '<?php if ($a){} $a ??= 1;',
            '<?php if ($a){} $a = $a ?? 1;',
        ];

        yield 'in call' => [
            '<?php foo($a ??= 1);',
            '<?php foo($a = $a ?? 1);',
        ];

        yield 'in call followed by end tag and ternary' => [
            '<?php foo( $a ??= 1 ) ?><?php $b = $b ? $c : $d ?>',
            '<?php foo( $a = $a ?? 1 ) ?><?php $b = $b ? $c : $d ?>',
        ];

        yield 'simple, before ) I' => [
            '<?php if ($a) $a ??= 1;',
            '<?php if ($a) $a = $a ?? 1;',
        ];

        yield 'simple, before ) II' => [
            '<?php
                if ($a) $a ??= 1;
                foreach ($d as $i) $a ??= 1;
                while (foo()) $a ??= 1;
            ',
            '<?php
                if ($a) $a = $a ?? 1;
                foreach ($d as $i) $a = $a ?? 1;
                while (foo()) $a = $a ?? 1;
            ',
        ];

        yield 'simple, end' => [
            '<?php $a ??= 1 ?>',
            '<?php $a = $a ?? 1 ?>',
        ];

        yield 'simple, 10x' => [
            '<?php'.str_repeat(' $a ??= 1;', 10),
            '<?php'.str_repeat(' $a = $a ?? 1;', 10),
        ];

        yield 'simple, multi line' => [
            '<?php
            $a
             ??=
              '.'
               '.'
                1;',
            '<?php
            $a
             =
              $a
               ??
                1;',
        ];

        yield 'dynamic var' => [
            '<?php ${beers::$ale} ??= 1;',
            '<?php ${beers::$ale} = ${beers::$ale} ?? 1;',
        ];

        yield [
            '<?php $argv ??= $_SERVER[\'argv\'] ?? [];',
            '<?php $argv = $argv ?? $_SERVER[\'argv\'] ?? [];',
        ];

        yield 'do not fix' => [
            '<?php
                $a = 1 + $a ?? $b;
                $b + $a = $a ?? 1;
                $b = $a ?? 1;
                $b = $a ?? $b;
                $d = $a + $c ; $c ?? $c;
                $a = ($a ?? $b) && $c; // just to be sure
                $a = (string) $a ?? 1;
                $a = 1 ?? $a;
            ',
        ];

        yield 'do not fix because of precedence 1' => [
            '<?php $a = $a ?? $b ? $c : $d;',
        ];

        yield 'do not fix because of precedence 2' => [
            '<?php $a = $a ?? $b ? $c : $d ?>',
        ];

        yield ['<?php $a[1][0] = $a ?? $a[1][0];'];

        yield 'switch case & default' => [
            '<?php
switch(foo()) {
    case 1:
        $a ??= 1;
        break;
    default:
        $b ??= 1;
        break;
}
',
            '<?php
switch(foo()) {
    case 1:
        $a = $a ?? 1;
        break;
    default:
        $b = $b ?? 1;
        break;
}
',
        ];

        yield 'operator precedence' => [
            '<?php $x = $z ? $b : $a = $a ?? 123;',
        ];

        yield 'alternative syntax' => [
            '<?php foreach([1, 2, 3] as $i): $a ??= 1; endforeach;',
            '<?php foreach([1, 2, 3] as $i): $a = $a ?? 1; endforeach;',
        ];

        yield 'assign and return' => [
            '<?php

class Foo
{
    private $test;

    public function bar($i)
    {
        return $this->test ??= $i;
    }
}',
            '<?php

class Foo
{
    private $test;

    public function bar($i)
    {
        return $this->test = $this->test ?? $i;
    }
}',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'mixed array' => [
            '<?php
                $a[1] ??= 1;
                $a{2} ??= 1;
                $a{2}[$f] ??= 1;
            ',
            '<?php
                $a[1] = $a[1] ?? 1;
                $a{2} = $a{2} ?? 1;
                $a{2}[$f] = $a{2}[$f] ?? 1;
            ',
        ];

        yield 'same II' => [
            '<?php $a[1] ??= 1;',
            '<?php $a[1] = $a{1} ?? 1;',
        ];

        yield 'same III' => [
            '<?php $a[1] ??= 1;',
            '<?php $a[1] = (($a{1})) ?? 1;',
        ];
    }
}
