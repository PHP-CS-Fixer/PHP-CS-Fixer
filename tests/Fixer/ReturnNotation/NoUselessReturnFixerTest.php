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

namespace PhpCsFixer\Tests\Fixer\ReturnNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class NoUselessReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
    function b($b) {
        if ($b) {
            return;
        }
         /**/
    }',
                '<?php
    function b($b) {
        if ($b) {
            return;
        }
        return /**/;
    }',
            ),
            array(
                '<?php
    class Test2
    {
        private static function a($a)
        {
            if ($a) {
                return;
            }

            $c1 = function() use ($a) {
                if ($a)
                    return;
                if ($a > 1) return;
                echo $a;
                '.'
            };
            $c1();
            '.'
            '.'
        }

        private function test()
        {
            $d = function(){
                echo 123;
                '.'
            };

            $d();
        }
    }',
                '<?php
    class Test2
    {
        private static function a($a)
        {
            if ($a) {
                return;
            }

            $c1 = function() use ($a) {
                if ($a)
                    return;
                if ($a > 1) return;
                echo $a;
                return;
            };
            $c1();
            return
            ;
        }

        private function test()
        {
            $d = function(){
                echo 123;
                return;
            };

            $d();
        }
    }',
            ),
            array(
                '<?php
    function aT($a) {
        if ($a) {
            return;
        }
                   '.'
    }',
                '<?php
    function aT($a) {
        if ($a) {
            return;
        }
        return           ;
    }',
            ),
            array(
                '<?php return;',
            ),
            array(
                '<?php
    function c($c) {
        if ($c) {
            return;
        }
        //'.'
    }',
                '<?php
    function c($c) {
        if ($c) {
            return;
        }
        return;//
    }',
            ),
            array(
                '<?php
    class Test {

        private static function d($d) {
            if ($d) {
                return;
            }
            }
    }',
                '<?php
    class Test {

        private static function d($d) {
            if ($d) {
                return;
            }
            return;}
    }',
            ),
            array(
                '<?php
    interface FooInterface
    {
        public function fnc();
    }',
            ),
            array(
                '<?php
    abstract class AbstractFoo
    {
        abstract public function fnc();
        abstract public function fnc1();
        static private function fn2(){}
        public function fnc3() {
            echo 1 . self::fn2();//{}
        }
    }',
            ),
            array(
                '<?php
    function foo () { }',
            ),
            array(
                '<?php
                $a = function() {
                       /**/
                     '.'
           /* a */   //
                    '.'
                };
                ',
                '<?php
                $a = function() {
                    return  ; /**/
                    return ;
           /* a */  return; //
                    return;
                };
                ',
            ),
        );
    }
}
