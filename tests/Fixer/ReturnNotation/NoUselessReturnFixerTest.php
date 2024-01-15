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

namespace PhpCsFixer\Tests\Fixer\ReturnNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer
 */
final class NoUselessReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                    function bar($baz)
                    {
                        if ($baz)
                            return $this->baz();
                        else
                            return;
                    }',
        ];

        yield [
            '<?php
                    function bar($baz)
                    {
                        if ($baz)
                            return $this->baz();
                        elseif($a)
                            return;
                    }',
        ];

        yield [
            '<?php
                    function bar($baz)
                    {
                        if ($baz)
                            return $this->baz();
                        else if($a)
                            return;
                    }',
        ];

        yield [
            '<?php
                    function bar($baz)
                    {
                        if ($baz)
                            return;
                    }',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php return;',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
    interface FooInterface
    {
        public function fnc();
    }',
        ];

        yield [
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
        ];

        yield [
            '<?php
    function foo () { }',
        ];

        yield [
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
        ];
    }
}
