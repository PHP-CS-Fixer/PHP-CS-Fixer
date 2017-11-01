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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer
 */
final class StaticLambdaFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'sample' => [
                "<?php\n\$a = static function () use (\$b)\n{   echo \$b;\n};",
                "<?php\n\$a = function () use (\$b)\n{   echo \$b;\n};",
            ],
            'minimal double fix case' => [
                '<?php $a=static function(){};$b=static function(){};',
                '<?php $a=function(){};$b=function(){};',
            ],
            [
                '<?php $a  /**/  =   /**/     static function(){};',
                '<?php $a  /**/  =   /**/     function(){};',
            ],
            [
                '<?php $a  /**/  =   /**/ static function(){};',
                '<?php $a  /**/  =   /**/ function(){};',
            ],
            [
                '<?php $a  /**/  =   /**/static function(){};',
                '<?php $a  /**/  =   /**/function(){};',
            ],
        ];
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideDoNotFixCases()
    {
        return [
            [
                '<?php
                    class A
                    {
                        public function B()
                        {
                            $a = function () {
                                $b = \'this\';
                                var_dump($$b);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class B
                    {
                        public function B()
                        {
                            $a = function () {
                                var_dump($THIS);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class C
                    {
                        public function B()
                        {
                            $a = function () {
                                $c = include __DIR__.\'/return_this.php\';
                                var_dump($c);
                            };

                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class D
                    {
                        public function B()
                        {
                            $a = function () {
                                $d = include_once __DIR__.\'/return_this.php\';
                                var_dump($d);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class E
                    {
                        public function B()
                        {
                            $a = function () {
                                $e = require_once __DIR__.\'/return_this.php\';
                                var_dump($e);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class F
                    {
                        public function B()
                        {
                            $a = function () {
                                $f = require __DIR__.\'/return_this.php\';
                                var_dump($f);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class G
                    {
                        public function B()
                        {
                            $a = function () {
                                $g = \'this\';
                                $h = ${$g};
                                var_dump($h);
                            };
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php
                    class H
                    {
                        public function B()
                        {
                            $a = function () {
                                $a = [0 => \'this\'];
                                var_dump(${$a[0]});
                            };
                        }
                    }
                ',
            ],
            [
                '<?php function test(){} test();',
            ],
            [
                '<?php class A
                {
                    function test(){}
                    public function test1(){}
                    protected function test2(){}
                    protected abstract function test3(){}
                    private function test3(){}
                }
                new A();',
            ],
            [
                '<?php

class A
{
    public function abc()
    {
        $b = function () {
            $c = eval(\'return $this;\');
            var_dump($c);
        };

        $b();
    }
}

$b = new A();
$b->abc();
',
            ],
        ];
    }
}
