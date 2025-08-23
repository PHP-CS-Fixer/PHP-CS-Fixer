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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CombineConsecutiveUnsetsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php //1
                    unset($foo/*;*/, /*;*/$bar, $c , $foobar  ,  $foobar2);
                     //test
                     /* more comment test*/
                    '.'
                ',
            '<?php //1
                    unset($foo/*;*/);
                    unset(/*;*/$bar, $c ); //test
                    unset($foobar  ); /* more comment test*/
                    unset(  $foobar2);
                ',
        ];

        yield [
            '<?php unset($d , $e);/*unset(    $d2);unset($e   );;*/    ',
            '<?php unset($d );/*unset(    $d2);unset($e   );;*/    uNseT($e);',
        ];

        yield [
            '<?php UNSET($a, $b,$c/**/); ',
            '<?php UNSET($a); unset($b,$c/**/);',
        ];

        yield [
            '<?php
              $config = array();
              if ($config) {
              }
              unset($config[\'autoescape_service\'], $config[\'autoescape_service_method\']);
              ',
        ];

        yield [
            '<?php //2
                    unset($foo, $bar, $foobar, $foobar2, $foobar3);/*1*/
                    /*2*/
                    //3
                    /*4*/
                    /*5*/ '.'
                ',
            '<?php //2
                    unset($foo);/*1*/
                    unset($bar);/*2*/
                    unset($foobar);//3
                    unset($foobar2);/*4*/
                    /*5*/ unset($foobar3);
                ',
        ];

        yield [
            '<?php
                    unset($foo3, $bar, $test,$test1);
                        /* c1 */
                        '.'
                '.'
                // c2
                '.'
                ',
            '<?php
                    unset($foo3);
                        /* c1 */
                        unset($bar);
                '.'
                // c2
                unset($test,$test1);
                ',
        ];

        yield [
            '<?php unset($x, $b  , $d);  /**/   ?> b',
            '<?php unset($x);  /**/ unset ($b  , $d) ?> b',
        ];

        yield [
            '<?php unset($x) ?>',
        ];

        yield [
            '<?php unset($y, $u); ?>',
            '<?php unset($y);unset($u) ?>',
        ];

        yield [
            '<?php
                    unset($a[0], $a[\'a\'], $a["b"], $a->b, $a->b->c, $a->b[0]->c[\'a\']);
                    '.'
                    '.'
                    '.'
                    '.'
                    '.'
                ',
            '<?php
                    unset($a[0]);
                    unset($a[\'a\']);
                    unset($a["b"]);
                    unset($a->b);
                    unset($a->b->c);
                    unset($a->b[0]->c[\'a\']);
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
     * @return iterable<int, array{string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php (unset)$f;',
        ];
    }
}
