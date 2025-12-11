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
 * @covers \PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TernaryToElvisOperatorFixerTest extends AbstractFixerTestCase
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
        $operators = ['+=', '-=', '*=', '**=', '/=', '.=', '%=', '&=', '|=', '^=', '<<=', '>>='];

        foreach ($operators as $operator) {
            yield \sprintf('Test with operator "%s".', $operator) => [
                \sprintf('<?php $z = $a %s $b ?  : $c;', $operator),
                \sprintf('<?php $z = $a %s $b ? $b : $c;', $operator),
            ];
        }

        yield 'multiple fixes' => [
            '<?php $a ?  : 1; $a ?  : 1; $a ?  : 1; $a ?  : 1; $a ?  : 1; $a ?  : 1; $a ?  : 1;',
            '<?php $a ? $a : 1; $a ? $a : 1; $a ? $a : 1; $a ? $a : 1; $a ? $a : 1; $a ? $a : 1; $a ? $a : 1;',
        ];

        yield [
            '<?php $z = $z ?  : "a";',
            '<?php $z = $z ? $z : "a";',
        ];

        yield [
            '<?php ${"foo"} ?  : "a";',
            '<?php ${"foo"} ? ${"foo"} : "a";',
        ];

        yield [
            '<?php $z::$a()[1] ?  : 1;',
            '<?php $z::$a()[1] ? $z::$a()[1] : 1;',
        ];

        yield [
            '<?php $z->$a ?  : 1;',
            '<?php $z->$a ? $z->$a : 1;',
        ];

        yield [
            '<?php $z = $z ?  : 1;',
            '<?php $z = $z ? $z : 1;',
        ];

        yield [
            '<?php $z = $z ?  : 1.1;',
            '<?php $z = $z ? $z : 1.1;',
        ];

        yield [
            '<?php $z = $a ?  : foo();',
            '<?php $z = $a ? $a : foo();',
        ];

        yield [
            '<?php $z = $a ?  : \foo();',
            '<?php $z = $a ? $a : \foo();',
        ];

        yield [
            '<?php $z = 1 ?  : $z;',
            '<?php $z = 1 ? 1 : $z;',
        ];

        yield [
            '<?php $z = 1.1 ?  : $z;',
            '<?php $z = 1.1 ? 1.1 : $z;',
        ];

        yield [
            '<?php $z = "a" ?  : "b";',
            '<?php $z = "a" ? "a" : "b";',
        ];

        yield [
            '<?php $z = foo() ?  : $a;',
            '<?php $z = foo() ? foo() : $a;',
        ];

        yield [
            '<?php $z = \foo() ?  : $a;',
            '<?php $z = \foo() ? \foo() : $a;',
        ];

        yield [
            '<?php 1 ?  : $z->$a;',
            '<?php 1 ? 1 : $z->$a;',
        ];

        yield [
            '<?php 1 ?  : $z::$a()[1];',
            '<?php 1 ? 1 : $z::$a()[1];',
        ];

        yield [
            '<?php $a ?  : ${"foo"};',
            '<?php $a ? $a : ${"foo"};',
        ];

        yield [
            '<?php {$b ?  : 1;}',
            '<?php {$b ? $b : 1;}',
        ];

        yield [
            '<?php {echo 1;} $c = $c ?  : 1;',
            '<?php {echo 1;} $c = $c ? $c : 1;',
        ];

        yield [
            '<?php $d ?  : 1;',
            '<?php $d ? ($d) : 1;',
        ];

        yield [
            '<?php $d ?  : 1;',
            '<?php $d ? (($d)) : 1;',
        ];

        yield [
            '<?php ($d) ?  : 1;',
            '<?php ($d) ? $d : 1;',
        ];

        yield [
            '<?php ($d) ?  : 1;',
            '<?php ($d) ? (($d)) : 1;',
        ];

        yield [
            '<?php
                a($d) ? $d : 1;
                $d ? a($d) : 1;
            ',
        ];

        yield [
            '<?php ; $e ?  : 1;',
            '<?php ; $e ? $e : 1;',
        ];

        yield [
            '<?php $foo8 = $bar[0] ?  : $foo;',
            '<?php $foo8 = $bar[0] ? $bar[0] : $foo;',
        ];

        yield [
            '<?php $foo7 = $_GET[$a] ?  : $foo;',
            '<?php $foo7 = $_GET[$a] ? $_GET[$a] : $foo;',
        ];

        yield [
            '<?php $foo6 = $bar[$a][0][$a ? 1 : 2][2] ? /* 1 *//* 2 *//* 3 */    /* 4 */ : $foo;',
            '<?php $foo6 = $bar[$a][0][$a ? 1 : 2][2] ? $bar/* 1 */[$a]/* 2 */[0]/* 3 */[$a ? 1 : 2]/* 4 */[2] : $foo;',
        ];

        yield [
            '<?php ; 2 ?  : 1;',
            '<?php ; 2 ? 2 : 1;',
        ];

        yield [
            '<?php
                $bar1[0][1] = $bar[0][1] ? $bar[0][1] + 1 : $bar[0][1];
                $bar2[0] = $bar[0] ? $bar[0] + 1 : $bar[0];

                $bar3[0][1] = $bar[0][1] ? ++$bar[0][1] : $bar[0][1];
                $bar4[0] = $bar[0] ? --$bar[0] : $bar[0];
            ',
        ];

        yield [
            '<?php
                $foo77 = $foo ? "$foo" : $foo;
                $foo77 = $foo ? \'$foo\' : $foo;
            ',
        ];

        yield 'comments 1' => [
            '<?php $a /* a */ = /* b */ $a /* c */ ? /* d */  /* e */ : /* f */ 1;',
            '<?php $a /* a */ = /* b */ $a /* c */ ? /* d */ $a /* e */ : /* f */ 1;',
        ];

        yield 'comments 2' => [
            '<?php $foo = $bar/* a */?/* b *//* c */:/* d */$baz;',
            '<?php $foo = $bar/* a */?/* b */$bar/* c */:/* d */$baz;',
        ];

        yield 'minimal' => [
            '<?php $b?:$c;',
            '<?php $b?$b:$c;',
        ];

        yield 'minimal 2x' => [
            '<?php $b?:$c;$f=$b?:$c;',
            '<?php $b?$b:$c;$f=$b?$b:$c;',
        ];

        yield [
            '<?php
                $foo = $bar
                    ? '.'
                    : $foo;
            ',
            '<?php
                $foo = $bar
                    ? $bar
                    : $foo;
            ',
        ];

        yield [
            '<?php
                $foo = $bar # 1
                    ?  # 2
                    : $foo; # 3
            ',
            '<?php
                $foo = $bar # 1
                    ? $bar # 2
                    : $foo; # 3
            ',
        ];

        yield [
            '<?php foo($a ?  : $b, $c ?  : $d);',
            '<?php foo($a ? $a : $b, $c ? $c : $d);',
        ];

        yield [
            '<?php $j[$b ?  : $c];',
            '<?php $j[$b ? $b : $c];',
        ];

        yield [
            '<?php foo($a[0] ?  : $b[0], $c[0] ?  : $d[0]);',
            '<?php foo($a[0] ? $a[0] : $b[0], $c[0] ? $c[0] : $d[0]);',
        ];

        yield [
            '<?php $a + 1 ?    : $b;',
            '<?php $a + 1 ? $a + 1 : $b;',
        ];

        yield [
            '<?php

$a ?  : <<<EOT

EOT;

$a ?  : <<<\'EOT\'

EOT;

<<<EOT

EOT
? '.'
: $a
;

<<<\'EOT\'

EOT
? '.'
: $a
;
',
            '<?php

$a ? $a : <<<EOT

EOT;

$a ? $a : <<<\'EOT\'

EOT;

<<<EOT

EOT
? <<<EOT

EOT
: $a
;

<<<\'EOT\'

EOT
? <<<\'EOT\'

EOT
: $a
;
',
        ];

        yield [
            '<?php @foo() ?  : 1;',
            '<?php @foo() ? @foo() : 1;',
        ];

        yield [
            '<?php
                $f = !foo() ?  : 1;
                $f = !$a ?  : 1;
                $f = $a[1][!$a][@foo()] ?  : 1;
                $f = !foo() ?  : 1;
            ',
            '<?php
                $f = !foo() ? !foo() : 1;
                $f = !$a ? !$a : 1;
                $f = $a[1][!$a][@foo()] ? $a[1][!$a][@foo()] : 1;
                $f = !foo() ? !foo() : 1;
            ',
        ];

        yield [
            '<?php $foo = $foo ? $bar : $foo;',
        ];

        yield [
            '<?php $foo1 = $bar[$a][0][1][2] ? 123 : $foo;',
        ];

        yield [
            '<?php $foo2 = $bar[$a] ? $bar[$b] : $foo;',
        ];

        yield [
            '<?php $foo2a = $bar[$a] ? $bar[$a][1] : $foo;',
        ];

        yield [
            '<?php $foo2b = $bar[$a][1] ? $bar[$a] : $foo;',
        ];

        yield [
            '<?php $foo3 = $bar[$a][1] ? $bar[$a][2] : $foo;',
        ];

        yield [
            '<?php $foo4 = 1 + $bar[0] ? $bar[0] : $foo;',
        ];

        yield [
            '<?php $foo = $bar ? $$bar : 1;',
        ];

        yield 'complex case 1 left out by design' => [
            '<?php $foo = !empty($bar) ? $bar : $baz;',
        ];

        yield 'complex case 2 left out by design' => [
            '<?php $foo = !!$bar ? $bar : $baz;',
        ];

        yield [
            '<?php $f = 1 + $f ? $f : 1;',
        ];

        yield [
            '<?php $g = $g ? $g - 1 : 1;',
        ];

        yield [
            '<?php
                $c = ++$a ? ++$a : $b;
                $c = (++$a) ? (++$a) : $b;
                $c = ($a++) ? ($a++) : $b;
                $c = fooBar(++$a) ? fooBar(++$a) : $b;
                $c = [++$a] ? [++$a] : $b;
            ',
        ];

        yield [
            '<?php
                $c = --$a ? --$a : $b;
                $c = (--$a) ? (--$a) : $b;
                $c = ($a--) ? ($a--) : $b;
                $c = fooBar(--$a) ? fooBar(--$a) : $b;
                $c = [--$a] ? [--$a] : $b;
            ',
        ];

        yield [
            '<?= $a ?  : $b ?>',
            '<?= $a ? $a : $b ?>',
        ];

        yield [
            '<?php new class() extends Foo {} ? new class{} : $a;',
        ];

        yield [
            '<?php $a ?  : new class{};',
            '<?php $a ? $a : new class{};',
        ];

        yield [
            '<?php $a ?  : new class($a) extends Foo {};',
            '<?php $a ? $a : new class($a) extends Foo {};',
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
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $foo = $a->{$b} ? $bar{0} : $foo;',
        ];

        yield [
            '<?php $l[$b[0] ?  : $c[0]];',
            '<?php $l[$b[0] ? $b{0} : $c[0]];',
        ];

        yield [
            '<?php $l{$b{0} ?  : $c{0}};',
            '<?php $l{$b{0} ? $b{0} : $c{0}};',
        ];

        yield [
            '<?php $z = $a[1][2] ?  : 1;',
            '<?php $z = $a[1][2] ? $a[1][2] : 1;',
        ];

        yield [
            '<?php $i = $bar{0}[1]{2}[3] ?  : $foo;',
            '<?php $i = $bar{0}[1]{2}[3] ? $bar{0}[1]{2}[3] : $foo;',
        ];

        yield [
            '<?php $fooX = $bar{0}[1]{2}[3] ?  : $foo;',
            '<?php $fooX = $bar{0}[1]{2}[3] ? $bar{0}[1]{2}[3] : $foo;',
        ];

        yield [
            '<?php $k = $bar{0} ?  : $foo;',
            '<?php $k = $bar{0} ? $bar{0} : $foo;',
        ];

        yield 'ignore different type of index braces' => [
            '<?php $z = $a[1] ?  : 1;',
            '<?php $z = $a[1] ? $a{1} : 1;',
        ];

        yield [
            '<?php __FILE__.$a.$b{2}.$c->$a[0] ?  : 1;',
            '<?php __FILE__.$a.$b{2}.$c->$a[0] ? __FILE__.$a.$b{2}.$c->$a[0] : 1;',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield ['<?php

function test(#[TestAttribute] ?User $user) {}
'];

        yield ['<?php

function test(#[TestAttribute] ?User $user = null) {}
'];
    }
}
