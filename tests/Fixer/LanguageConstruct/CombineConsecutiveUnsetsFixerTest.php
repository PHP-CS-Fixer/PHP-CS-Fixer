<?php

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
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer
 */
final class CombineConsecutiveUnsetsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
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
            ],
            [
                '<?php unset($d , $e);/*unset(    $d2);unset($e   );;*/    ',
                '<?php unset($d );/*unset(    $d2);unset($e   );;*/    uNseT($e);',
            ],
            [
                '<?php UNSET($a, $b,$c/**/); ',
                '<?php UNSET($a); unset($b,$c/**/);',
            ],
            [
              '<?php
              $config = array();
              if ($config) {
              }
              unset($config[\'autoescape_service\'], $config[\'autoescape_service_method\']);
              ',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php (unset)$f;',
            ],
            [
                '<?php unset($x, $b  , $d);  /**/   ?> b',
                '<?php unset($x);  /**/ unset ($b  , $d) ?> b',
            ],
            [
                '<?php unset($x) ?>',
            ],
            [
                '<?php unset($y, $u); ?>',
                '<?php unset($y);unset($u) ?>',
            ],
            [
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
            ],
        ];
    }
}
