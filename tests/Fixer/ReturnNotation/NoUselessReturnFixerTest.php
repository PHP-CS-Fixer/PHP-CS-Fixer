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

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    function bar($baz)
                                    {
                                        if ($baz)
                                            return $this->baz();
                                        else
                                            return;
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    function bar($baz)
                                    {
                                        if ($baz)
                                            return $this->baz();
                                        elseif($a)
                                            return;
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    function bar($baz)
                                    {
                                        if ($baz)
                                            return $this->baz();
                                        else if($a)
                                            return;
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    function bar($baz)
                                    {
                                        if ($baz)
                                            return;
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function b($b) {
                        if ($b) {
                            return;
                        }
                         /**/
                    }
                EOD,
            <<<'EOD'
                <?php
                    function b($b) {
                        if ($b) {
                            return;
                        }
                        return /**/;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD."\n                ".<<<'EOD'

                            };
                            $c1();
                EOD."\n            ".''."\n            ".<<<'EOD'

                        }

                        private function test()
                        {
                            $d = function(){
                                echo 123;
                EOD."\n                ".<<<'EOD'

                            };

                            $d();
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function aT($a) {
                        if ($a) {
                            return;
                        }
                EOD."\n                   ".<<<'EOD'

                    }
                EOD,
            <<<'EOD'
                <?php
                    function aT($a) {
                        if ($a) {
                            return;
                        }
                        return           ;
                    }
                EOD,
        ];

        yield [
            '<?php return;',
        ];

        yield [
            <<<'EOD'
                <?php
                    function c($c) {
                        if ($c) {
                            return;
                        }
                        //
                EOD.<<<'EOD'

                    }
                EOD,
            <<<'EOD'
                <?php
                    function c($c) {
                        if ($c) {
                            return;
                        }
                        return;//
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Test {

                        private static function d($d) {
                            if ($d) {
                                return;
                            }
                            }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Test {

                        private static function d($d) {
                            if ($d) {
                                return;
                            }
                            return;}
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    interface FooInterface
                    {
                        public function fnc();
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    abstract class AbstractFoo
                    {
                        abstract public function fnc();
                        abstract public function fnc1();
                        static private function fn2(){}
                        public function fnc3() {
                            echo 1 . self::fn2();//{}
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo () { }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                $a = function() {
                                       /**/
                EOD."\n                     ".<<<'EOD'

                           /* a */   //
                EOD."\n                    ".<<<'EOD'

                                };
                EOD."\n                ",
            <<<'EOD'
                <?php
                                $a = function() {
                                    return  ; /**/
                                    return ;
                           /* a */  return; //
                                    return;
                                };
                EOD."\n                ",
        ];
    }
}
