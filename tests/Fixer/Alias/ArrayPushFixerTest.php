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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\ArrayPushFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Alias\ArrayPushFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ArrayPushFixerTest extends AbstractFixerTestCase
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
        yield 'minimal' => [
            '<?php $a[] =$b;',
            '<?php array_push($a,$b);',
        ];

        yield 'simple' => [
            '<?php $a[] = $b;',
            '<?php array_push($a, $b);',
        ];

        yield 'simple, spaces' => [
            '<?php  $a   [] =$b ;',
            '<?php array_push( $a ,  $b );',
        ];

        yield 'simple 25x times' => [
            '<?php '.str_repeat('$a[] = $b;', 25),
            '<?php '.str_repeat('array_push($a, $b);', 25),
        ];

        yield 'simple namespaced' => [
            '<?php $a[] = $b1;',
            '<?php \array_push($a, $b1);',
        ];

        yield '; before' => [
            '<?php ; $a1[] = $b2 ?>',
            '<?php ; array_push($a1, $b2) ?>',
        ];

        yield ') before' => [
            '<?php
                if ($c) $a[] = $b;

                while (--$c > 0) $a[] = $c;
            ',
            '<?php
                if ($c) array_push($a, $b);

                while (--$c > 0) array_push($a, $c);
            ',
        ];

        yield '} before' => [
            '<?php $b3 = []; { $a = 1; } $b5[] = 1;',
            '<?php $b3 = []; { $a = 1; } \array_push($b5, 1);',
        ];

        yield '{ before' => [
            '<?php { $a[] = $b8; }',
            '<?php { array_push($a, $b8); }',
        ];

        yield 'comments and PHPDoc' => [
            '<?php /* */ $a2[] = $b3 /** */;',
            '<?php /* */ array_push($a2, $b3) /** */;',
        ];

        yield [
            '<?php $a4[1][] = $b6[2];',
            '<?php array_push($a4[1], $b6[2]);',
        ];

        yield 'case insensitive and precedence' => [
            '<?php
                $a[] = $b--;
                $a[] = ++$b;
                $a[] = !$b;
                $a[] = $b + $c;
                $a[] = 1 ** $c / 2 || !b && c(1,2,3) ^ $a[1];
            ',
            '<?php
                array_push($a, $b--);
                ARRAY_push($a, ++$b);
                array_PUSH($a, !$b);
                ARRAY_PUSH($a, $b + $c);
                \array_push($a, 1 ** $c / 2 || !b && c(1,2,3) ^ $a[1]);
            ',
        ];

        yield 'simple traditional array' => [
            '<?php $a[] = array($b, $c);',
            '<?php array_push($a, array($b, $c));',
        ];

        yield 'simple short array' => [
            '<?php $a[] = [$b];',
            '<?php array_push($a, [$b]);',
        ];

        yield 'multiple element short array' => [
            '<?php $a[] = [[], [], $b, $c];',
            '<?php array_push($a, [[], [], $b, $c]);',
        ];

        yield 'second argument wrapped in `(` `)`' => [
            '<?php $a::$c[] = ($b);',
            '<?php array_push($a::$c, ($b));',
        ];

        yield [
            '<?php $a::$c[] = $b;',
            '<?php array_push($a::$c, $b);',
        ];

        yield [
            '<?php $a[foo(1,2,3)][] = $b[foo(1,2,3)];',
            '<?php array_push($a[foo(1,2,3)], $b[foo(1,2,3)]);',
        ];

        yield [
            '<?php \A\B::$foo[] = 1;',
            '<?php array_push(\A\B::$foo, 1);',
        ];

        yield [
            '<?php static::$foo[] = 1;',
            '<?php array_push(static::$foo, 1);',
        ];

        yield [
            '<?php namespace\A::$foo[] = 1;',
            '<?php array_push(namespace\A::$foo, 1);',
        ];

        yield [
            '<?php foo()->bar[] = 1;',
            '<?php array_push(foo()->bar, 1);',
        ];

        yield [
            '<?php foo()[] = 1;',
            '<?php array_push(foo(), 1);',
        ];

        yield [
            '<?php $a->$c[] = $b;',
            '<?php array_push($a->$c, $b);',
        ];

        yield [
            '<?php $a->$c[1]->$d[$a--]->$a[7][] = $b;',
            '<?php array_push($a->$c[1]->$d[$a--]->$a[7], $b);',
        ];

        yield 'push multiple' => [
            '<?php array_push($a6, $b9, $c);',
        ];

        yield 'push multiple II' => [
            '<?php ; array_push($a6a, $b9->$a(1,2), $c);',
        ];

        yield 'push multiple short' => [
            '<?php array_push($a6, [$b,$c], []);',
        ];

        yield 'returns number of elements in the array I' => [
            '<?php foo(array_push($a7, $b10)); // ;array_push($a, $b);',
        ];

        yield 'returns number of elements in the array II' => [
            '<?php $a = 3 * array_push($a8, $b11);',
        ];

        yield 'returns number of elements in the array III' => [
            '<?php $a = foo($z, array_push($a9, $b12));',
        ];

        yield 'returns number of elements in the array IV' => [
            '<?php $z = array_push($a00, $b13);',
        ];

        yield 'function declare in different namespace' => [
            '<?php namespace Foo; function array_push($a11, $b14){};',
        ];

        yield 'overridden detect I' => [
            '<?php namespace Foo; array_push(1, $a15);',
        ];

        yield 'overridden detect II' => [
            '<?php namespace Foo; array_push($a + 1, $a16);',
        ];

        yield 'different namespace and not a function call' => [
            '<?php
                A\array_push($a, $b17);
                A::array_push($a, $b18);
                $a->array_push($a, $b19);
            ',
        ];

        yield 'open echo' => [
            '<?= array_push($a, $b20) ?> <?= array_push($a, $b20); ?>',
        ];

        yield 'ellipsis' => [
            '<?php array_push($a, ...$b21);',
        ];

        $precedenceCases = [
            '$b and $c',
            '$b or $c',
            '$b xor $c',
        ];

        foreach ($precedenceCases as $precedenceCase) {
            yield [
                \sprintf(
                    '<?php array_push($a, %s);',
                    $precedenceCase,
                ),
            ];
        }

        yield [
            '<?php
                while (foo()) $a[] = $b;
                foreach (foo() as $C) $a[] = $b;
                if (foo()) $a[] = $b;
                if ($b) {} elseif (foo()) $a[] = $b;
            ',
            '<?php
                while (foo()) array_push($a, $b);
                foreach (foo() as $C) array_push($a, $b);
                if (foo()) array_push($a, $b);
                if ($b) {} elseif (foo()) array_push($a, $b);
            ',
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
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $a5{1*3}[2+1][] = $b4{2+1};',
            '<?php array_push($a5{1*3}[2+1], $b4{2+1});',
        ];

        yield [
            '<?php $a5{1*3}[2+1][] = $b7{2+1};',
            '<?php array_push($a5{1*3}[2+1], $b7{2+1});',
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
        yield [
            '<?php array_push($b?->c[2], $b19);',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'simple 8.1' => [
            '<?php
                $a[] = $b;
                $a = array_push(...);
            ',
            '<?php
                array_push($a, $b);
                $a = array_push(...);
            ',
        ];
    }

    /**
     * @dataProvider provideFixPre84Cases
     *
     * @requires PHP <8.4
     */
    public function testFixPre84(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre84Cases(): iterable
    {
        yield [
            '<?php $a->$c[1]->$d{$a--}->$a[7][] = $b;',
            '<?php array_push($a->$c[1]->$d{$a--}->$a[7], $b);',
        ];
    }
}
