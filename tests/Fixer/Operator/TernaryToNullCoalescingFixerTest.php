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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer
 */
final class TernaryToNullCoalescingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield from [
            // Do not fix cases.
            ['<?php $x = isset($a) ? $a[1] : null;'],
            ['<?php $x = isset($a) and $a ? $a : "";'],
            ['<?php $x = "isset($a) ? $a : null";'],
            ['<?php $x = isset($a) ? $$a : null;'],
            ['<?php $x = isset($a) ? "$a" : null;'],
            ['<?php $x = isset($a) ?: false;'],
            ['<?php $x = $y ?? isset($a) ? $a : null;'],
            ['<?php $x = isset($a) ?: $b;'],
            ['<?php $x = isset($a, $b) ? $a : null;'],
            ['<?php $x = $a && isset($b) ? $b : null;'],
            ['<?php $x = $a & isset($b) ? $b : null;'],
            ['<?php $x = ! isset($a) ? $a : null;'],
            ['<?php $x = false === isset($a) ? $a : 2;'],
            ['<?php $x = 4 * isset($a) ? $a : 2;'],
            ['<?php $x = "4" . isset($a) ? $a : 2;'],
            ['<?php $x = 3 ** isset($a) ? $a : 2;'],
            ['<?php $x = 1 | isset($a) ? $a : 2;'],
            ['<?php $x = (array) isset($a) ? $a : 2;'],
            ['<?php $x = isset($a[++$i]) ? $a[++$i] : null;'],
            ['<?php $x = function(){isset($a[yield]) ? $a[yield] : null;};'],
            ['<?php $x = isset($a[foo()]) ? $a[foo()] : null;'],
            ['<?php $x = isset($a[$callback()]) ? $a[$callback()] : null;'],
            ['<?php $y = isset($a) ? 2**3 : 3**2;'],
            ['<?php $x = function(){isset($a[yield from $a]) ? $a[yield from $a] : null;};'],
            // Fix cases.
            'Common fix case (I).' => [
                '<?php $x = $a ?? null;',
                '<?php $x = isset($a) ? $a : null;',
            ],
            'Common fix case (II).' => [
                '<?php $x = $a[0] ?? 1;',
                '<?php $x = isset($a[0]) ? $a[0] : 1;',
            ],
            'Minimal number of tokens case.' => [
                '<?php
$x=$a??null?>',
                '<?php
$x=isset($a)?$a:null?>',
            ],
            [
                '<?php $x = $a ?? 1; $y = isset($b) ? "b" : 2; $x = $c ?? 3;',
                '<?php $x = isset($a) ? $a : 1; $y = isset($b) ? "b" : 2; $x = isset($c) ? $c : 3;',
            ],
            [
                '<?php $x = $a[ $b[ "c"  ]]   ?? null;',
                '<?php $x = isset   (  $a[$b["c"]]) ?$a[ $b[ "c"  ]]   : null;',
            ],
            [
                '<?php $x = $a ?? $b[func(1, true)];',
                '<?php $x = isset($a) ? $a : $b[func(1, true)];',
            ],
            [
                '<?php $x = $a ?? ($b ?? "");',
                '<?php $x = isset($a) ? $a : (isset($b) ? $b : "");',
            ],
            [
                '<?php $x = ($a ?? isset($b)) ? $b : "";',
                '<?php $x = (isset($a) ? $a : isset($b)) ? $b : "";',
            ],
            [
                '<?php $x = $obj->a ?? null;',
                '<?php $x = isset($obj->a) ? $obj->a : null;',
            ],
            [
                '<?php $x = $obj->a->b ?? null;',
                '<?php $x = isset($obj->a->b) ? $obj->a->b : null;',
            ],
            [
                '<?php $x = $obj->$a ?? null;',
                '<?php $x = isset($obj->$a) ? $obj->$a : null;',
            ],
            [
                '<?php $x = $obj->a->$b ?? null;',
                '<?php $x = isset($obj->a->$b) ? $obj->a->$b : null;',
            ],
            [
                '<?php $x = $obj->a[3] ?? null;',
                '<?php $x = isset($obj->a[3]) ? $obj->a[3] : null;',
            ],
            [
                '<?php $x = $obj->a[\'foo\'] ?? null;',
                '<?php $x = isset($obj->a[\'foo\']) ? $obj->a[\'foo\'] : null;',
            ],
            [
                '<?php $x = $obj->a[$b] ?? null;',
                '<?php $x = isset($obj->a[$b]) ? $obj->a[$b] : null;',
            ],
            [
                '<?php $x = $obj->a[$b][\'foo\'] ?? null;',
                '<?php $x = isset($obj->a[$b][\'foo\']) ? $obj->a[$b][\'foo\'] : null;',
            ],
            [
                '<?php $x = $obj->a[$b[\'foo\']] ?? null;',
                '<?php $x = isset($obj->a[$b[\'foo\']]) ? $obj->a[$b[\'foo\']] : null;',
            ],
            [
                '<?php $x = $a[$obj->b] ?? null;',
                '<?php $x = isset($a[$obj->b]) ? $a[$obj->b] : null;',
            ],
            [
                '<?php $x = Foo::A[$b] ?? null;',
                '<?php $x = isset(Foo::A[$b]) ? Foo::A[$b] : null;',
            ],
            [
                '<?php $x = $a[Foo::B] ?? null;',
                '<?php $x = isset($a[Foo::B]) ? $a[Foo::B] : null;',
            ],
            [
                '<?php $x = (
// c1
// c2
// c3
$a
// c4
??
// c5
null
/* c6 */
)
# c7
;',
                '<?php $x = (
// c1
isset($a)
// c2
?
// c3
$a
// c4
:
// c5
null
/* c6 */
)
# c7
;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield ['<?php $x = $a ? $a : isset($b) ? $b : isset($c) ? $c : "";'];

        yield [
            '<?php $x = $a ?? isset($b) ? $b : isset($c) ? $c : "";',
            '<?php $x = isset($a) ? $a : isset($b) ? $b : isset($c) ? $c : "";',
        ];

        yield [
            '<?php $x = /*a1*//*a2*/ /*b*/ $a /*c*/ ?? /*d*/ isset($b) /*e*/ ? /*f*/ $b /*g*/ : /*h*/ isset($c) /*i*/ ? /*j*/ $c /*k*/ : /*l*/ "";',
            '<?php $x = isset($a) /*a1*//*a2*/ ? /*b*/ $a /*c*/ : /*d*/ isset($b) /*e*/ ? /*f*/ $b /*g*/ : /*h*/ isset($c) /*i*/ ? /*j*/ $c /*k*/ : /*l*/ "";',
        ];

        yield [
            '<?php $x = /*a1*//*a2*/ /*b*/ $a /*c*/ ?? /*d*/ isset($b) /*e*/ ? /*f*/ $b /*g*/ : /*h*/ isset($c) /*i*/ ? /*j*/ $c /*k*/ : /*l*/ "";',
            '<?php $x = isset($a) /*a1*//*a2*/ ? /*b*/ $a /*c*/ : /*d*/ isset($b) /*e*/ ? /*f*/ $b /*g*/ : /*h*/ isset($c) /*i*/ ? /*j*/ $c /*k*/ : /*l*/ "";',
        ];
    }
}
