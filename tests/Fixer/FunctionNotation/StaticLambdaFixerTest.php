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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer
 */
final class StaticLambdaFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
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
            [
                '<?php $a=static fn() => null;$b=static fn() => null;',
                '<?php $a=fn() => null;$b=fn() => null;',
            ],
            [
                '<?php $a  /**/  =   /**/     static fn() => null;',
                '<?php $a  /**/  =   /**/     fn() => null;',
            ],
            [
                '<?php $a  /**/  =   /**/ static fn() => null;',
                '<?php $a  /**/  =   /**/ fn() => null;',
            ],
            [
                '<?php $a  /**/  =   /**/static fn() => null; echo $this->foo();',
                '<?php $a  /**/  =   /**/fn() => null; echo $this->foo();',
            ],
            [
                '<?php $a  /**/  =   /**/ static fn() => null ?> <?php echo $this->foo();',
                '<?php $a  /**/  =   /**/ fn() => null ?> <?php echo $this->foo();',
            ],
            [
                '<?php
                    class B
                    {
                        public function C()
                        {
                            $a = fn () => var_dump($this);
                            $a();
                        }
                    }
                ',
            ],
            [
                '<?php static fn($a = ["foo" => "bar"]) => [];',
                '<?php fn($a = ["foo" => "bar"]) => [];',
            ],
            [
                '<?php class Foo {
                    public function getNames()
                    {
                        return \array_map(
                            static fn ($item) => $item->getName(),
                            $this->getItems()
                        );
                    }
                }',
                '<?php class Foo {
                    public function getNames()
                    {
                        return \array_map(
                            fn ($item) => $item->getName(),
                            $this->getItems()
                        );
                    }
                }',
            ],
            [
                '<?php class Foo {
                    public function getNames()
                    {
                        return \array_map(
                            fn ($item) => $item->getName(1, $this->foo()),
                            $this->getItems()
                        );
                    }
                }',
            ],
        ];
    }

    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideDoNotFixCases(): array
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
                        public function C()
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
                    class D
                    {
                        public function E()
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
                    class F
                    {
                        public function G()
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
                    class H
                    {
                        public function I()
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
                    class J
                    {
                        public function K()
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
                    class L
                    {
                        public function M()
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
                    class N
                    {
                        public function O()
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
                '<?php abstract class P
                {
                    function test0(){}
                    public function test1(){}
                    protected function test2(){}
                    protected abstract function test3();
                    private function test4(){}
                }
                ',
            ],
            [
                '<?php

class Q
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

$b = new Q();
$b->abc();
',
            ],
            [
                '<?php

                class A {}
                class B extends A {
                    public function foo()
                    {
                        $c = function () {
                            return parent::foo();
                        };
                    }
                }',
            ],
            [
                '<?php
                    class B
                    {
                        public function C()
                        {
                            return array_map(
                                fn () => $this,
                                []
                            );
                        }
                    }
                ',
            ],
        ];
    }
}
