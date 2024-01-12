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
 * @covers \PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer
 */
final class ReturnAssignmentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixNestedFunctionsCases
     */
    public function testFixNestedFunctions(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixNestedFunctionsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                function A($a0,$a1,$a2,$d)
                {
                    if ($a0) {
                        return 1;
                          // fix me
                    }

                    if ($a1) {
                        return 2;
                          // fix me
                    }

                    $nested0 = function() {
                        global $a;

                        ++$a;
                        $d = 2;

                        $nested1 = function () use ($d) {
                            if ($d) {
                                return 3;
                                  // fix me
                            }

                            $nested2 = function (&$d) {
                                if ($d) {
                                    $f = 1;
                                    return $f; // fix me not
                                }

                                $d = function () {
                                    return 4;
                                      // fix me
                                };

                                if ($d+1) {
                                    $f = 1;
                                    return $f; // fix me not
                                }
                            };

                            return $nested2();
                        };

                        return $a; // fix me not
                    };

                    if ($a2) {
                        return 5;
                          // fix me
                    }
                }

                function B($b0, $b1, $b2)
                {
                    if ($b0) {
                        return 10;
                          // fix me
                    }

                    if ($b1) {
                        return 20;
                          // fix me
                    }

                    if ($b2) {
                        return 30;
                          // fix me
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                function A($a0,$a1,$a2,$d)
                {
                    if ($a0) {
                        $b = 1;
                        return $b; // fix me
                    }

                    if ($a1) {
                        $c = 2;
                        return $c; // fix me
                    }

                    $nested0 = function() {
                        global $a;

                        ++$a;
                        $d = 2;

                        $nested1 = function () use ($d) {
                            if ($d) {
                                $f = 3;
                                return $f; // fix me
                            }

                            $nested2 = function (&$d) {
                                if ($d) {
                                    $f = 1;
                                    return $f; // fix me not
                                }

                                $d = function () {
                                    $a = 4;
                                    return $a; // fix me
                                };

                                if ($d+1) {
                                    $f = 1;
                                    return $f; // fix me not
                                }
                            };

                            return $nested2();
                        };

                        return $a; // fix me not
                    };

                    if ($a2) {
                        $d = 5;
                        return $d; // fix me
                    }
                }

                function B($b0, $b1, $b2)
                {
                    if ($b0) {
                        $b = 10;
                        return $b; // fix me
                    }

                    if ($b1) {
                        $c = 20;
                        return $c; // fix me
                    }

                    if ($b2) {
                        $d = 30;
                        return $d; // fix me
                    }
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        return 15;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        $a = 15;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        /*0*/return /*1*//*2*/15;/*3*//*4*/ /*5*/ /*6*//*7*//*8*/
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        /*0*/$a/*1*/=/*2*/15;/*3*//*4*/ /*5*/ return/*6*/$a/*7*/;/*8*/
                                    }
                EOD."\n                ",
        ];

        yield 'comments with leading space' => [
            <<<'EOD'
                <?php
                                    function A()
                                    { #1
                 #2
                 return #3
                 #4
                  #5
                 #6
                 15 #7
                 ; #8
                 #9
                  #10
                 #11
                   #12
                 #13
                   #14
                 #15
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    { #1
                 #2
                 $a #3
                 #4
                 = #5
                 #6
                 15 #7
                 ; #8
                 #9
                 return #10
                 #11
                 $a  #12
                 #13
                 ;  #14
                 #15
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    abstract class B
                                    {
                                        abstract protected function Z();public function A()
                                        {
                                            return 16;
                                        }
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    abstract class B
                                    {
                                        abstract protected function Z();public function A()
                                        {
                                            $a = 16; return $a;
                                        }
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    function b() {
                                        if ($c) {
                                            return 0;
                                        }
                                        return testFunction(654+1);
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function b() {
                                        if ($c) {
                                            $b = 0;
                                            return $b;
                                        }
                                        $a = testFunction(654+1);
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'minimal notation' => [
            '<?php $e=function(){return 1;};$f=function(){return 1;};$g=function(){return 1;};',
            '<?php $e=function(){$a=1;return$a;};$f=function(){$a=1;return$a;};$g=function(){$a=1;return$a;};',
        ];

        yield [
            <<<'EOD'
                <?php
                                    function A()
                                    {#1
                #2
                EOD.'                    '.<<<'EOD'

                return #3
                #4
                #5
                #6
                15#7
                ;#8
                #9
                #10
                #11
                #12
                #13
                #14
                #15
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {#1
                #2
                EOD.'                    '.<<<'EOD'

                $a#3
                #4
                =#5
                #6
                15#7
                ;#8
                #9
                return#10
                #11
                $a#12
                #13
                ;#14
                #15
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                function A($b)
                {
                        // Comment
                        return a("2", 4, $b);
                }

                EOD,
            <<<'EOD'
                <?php
                function A($b)
                {
                        // Comment
                        $value = a("2", 4, $b);

                        return $value;
                }

                EOD,
        ];

        yield [
            '<?php function a($b,$c)  {if($c>1){echo 1;} return (1 + 2 + $b);  }',
            '<?php function a($b,$c)  {if($c>1){echo 1;} $a= (1 + 2 + $b);return $a; }',
        ];

        yield [
            '<?php function a($b,$c)  {return (3 * 4 + $b);  }',
            '<?php function a($b,$c)  {$zz= (3 * 4 + $b);return $zz; }',
        ];

        yield [
            <<<'EOD'
                <?php
                                    function a() {
                                        return 4563;
                                          ?> <?php
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function a() {
                                        $a = 4563;
                                        return $a ?> <?php
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        return $c + 1; /*
                var names are case-insensitive */ }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        $A = $c + 1; /*
                var names are case-insensitive */ return $a   ;}
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        return $f[1]->a();
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        $a = $f[1]->a();
                                        return $a;
                                    }
                EOD."\n                ",
            [
                <<<'EOD'
                    <?php
                                        function a($foos) {
                                            return array_map(function ($foo) {
                                                return (string) $foo;
                                            }, $foos);
                                        }
                    EOD,
                <<<'EOD'
                    <?php
                                        function a($foos) {
                                            $bars = array_map(function ($foo) {
                                                return (string) $foo;
                                            }, $foos);

                                            return $bars;
                                        }
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                                        function a($foos) {
                                            return ($foos = ['bar']);
                                        }
                    EOD,
                <<<'EOD'
                    <?php
                                        function a($foos) {
                                            $bars = ($foos = ['bar']);

                                            return $bars;
                                        }
                    EOD,
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                                    function a($foos) {
                                        return (function ($foos) {
                                            return $foos;
                                        })($foos);
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    function a($foos) {
                                        $bars = (function ($foos) {
                                            return $foos;
                                        })($foos);

                                        return $bars;
                                    }
                EOD,
        ];

        yield 'anonymous classes' => [
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        return new class {};
                                    }

                                    function B()
                                    {
                                        return new class() {};
                                    }

                                    function C()
                                    {
                                        return new class(1,2) { public function Z(Foo $d){} };
                                    }

                                    function D()
                                    {
                                        return new class extends Y {};
                                    }

                                    function E()
                                    {
                                        return new class extends Y implements A\O,P {};
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        $a = new class {};
                                        return $a;
                                    }

                                    function B()
                                    {
                                        $b = new class() {};
                                        return $b;
                                    }

                                    function C()
                                    {
                                        $c = new class(1,2) { public function Z(Foo $d){} };
                                        return $c;
                                    }

                                    function D()
                                    {
                                        $c = new class extends Y {};
                                        return $c;
                                    }

                                    function E()
                                    {
                                        $c = new class extends Y implements A\O,P {};
                                        return $c;
                                    }
                EOD."\n                ",
        ];

        yield 'lambda' => [
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        return function () {};
                                    }

                                    function B()
                                    {
                                        return function ($a, $b) use ($z) {};
                                    }

                                    function C()
                                    {
                                        return static function ($a, $b) use ($z) {};
                                    }

                                    function D()
                                    {
                                        return function &() use(&$b) {
                                            return $b; // do not fix
                                        };
                                          // fix
                                    }

                                    function E()
                                    {
                                        return function &() {
                                            $z = new A(); return $z; // do not fix
                                        };
                                          // fix
                                    }

                                    function A99()
                                    {
                                        $v = static function ($a, $b) use ($z) {};
                                        return 15;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function A()
                                    {
                                        $a = function () {};

                                        return $a;
                                    }

                                    function B()
                                    {
                                        $b = function ($a, $b) use ($z) {};

                                        return $b;
                                    }

                                    function C()
                                    {
                                        $c = static function ($a, $b) use ($z) {};

                                        return $c;
                                    }

                                    function D()
                                    {
                                        $a = function &() use(&$b) {
                                            return $b; // do not fix
                                        };

                                        return $a; // fix
                                    }

                                    function E()
                                    {
                                        $a = function &() {
                                            $z = new A(); return $z; // do not fix
                                        };

                                        return $a; // fix
                                    }

                                    function A99()
                                    {
                                        $v = static function ($a, $b) use ($z) {};
                                        $a = 15;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'arrow functions' => [
            <<<'EOD'
                <?php
                                    function Foo() {
                                        return fn($x) => $x + $y;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function Foo() {
                                        $fn1 = fn($x) => $x + $y;

                                        return $fn1;
                                    }
                EOD."\n                ",
        ];

        yield 'try catch' => [
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    if (isSomeCondition()) {
                                        return getSomeResult();
                                    }

                                    try {
                                        $result = getResult();

                                        return $result;
                                    } catch (\Throwable $exception) {
                                        baz($result ?? null);
                                    }
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    if (isSomeCondition()) {
                                        $result = getSomeResult();

                                        return $result;
                                    }

                                    try {
                                        $result = getResult();

                                        return $result;
                                    } catch (\Throwable $exception) {
                                        baz($result ?? null);
                                    }
                                }
                EOD."\n                ",
        ];

        yield 'multiple try/catch blocks separated with conditional return' => [
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    try {
                                        return getResult();
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    }

                                    if (isSomeCondition()) {
                                        return getSomeResult();
                                    }

                                    try {
                                        $result = $a + $b;
                                        return $result;
                                    } catch (\Throwable $th) {
                                        var_dump($result ?? null);
                                    }
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    try {
                                        $result = getResult();
                                        return $result;
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    }

                                    if (isSomeCondition()) {
                                        $result = getSomeResult();
                                        return $result;
                                    }

                                    try {
                                        $result = $a + $b;
                                        return $result;
                                    } catch (\Throwable $th) {
                                        var_dump($result ?? null);
                                    }
                                }
                EOD."\n                ",
        ];

        yield 'try/catch/finally' => [
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    if (isSomeCondition()) {
                                        return getSomeResult();
                                    }

                                    try {
                                        $result = getResult();

                                        return $result;
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    } finally {
                                        baz($result);
                                    }
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    if (isSomeCondition()) {
                                        $result = getSomeResult();

                                        return $result;
                                    }

                                    try {
                                        $result = getResult();

                                        return $result;
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    } finally {
                                        baz($result);
                                    }
                                }
                EOD."\n                ",
        ];

        yield 'multiple try/catch separated with conditional return, with finally block' => [
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    try {
                                        return getResult();
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    }

                                    if (isSomeCondition()) {
                                        return getSomeResult();
                                    }

                                    try {
                                        $result = $a + $b;
                                        return $result;
                                    } catch (\Throwable $th) {
                                        throw $th;
                                    } finally {
                                        echo "result:", $result, \PHP_EOL;
                                    }
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo()
                                {
                                    try {
                                        $result = getResult();
                                        return $result;
                                    } catch (\Throwable $exception) {
                                        error_log($exception->getMessage());
                                    }

                                    if (isSomeCondition()) {
                                        $result = getSomeResult();
                                        return $result;
                                    }

                                    try {
                                        $result = $a + $b;
                                        return $result;
                                    } catch (\Throwable $th) {
                                        throw $th;
                                    } finally {
                                        echo "result:", $result, \PHP_EOL;
                                    }
                                }
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideDoNotFixCases(): iterable
    {
        yield 'invalid reference stays invalid' => [
            <<<'EOD'
                <?php
                                    function bar() {
                                        $foo = &foo();
                                        return $foo;
                                }
                EOD,
        ];

        yield 'static' => [
            <<<'EOD'
                <?php
                                    function a() {
                                        static $a;
                                        $a = time();
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'global' => [
            <<<'EOD'
                <?php
                                    function a() {
                                        global $a;
                                        $a = time();
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'passed by reference' => [
            <<<'EOD'
                <?php
                                function foo(&$var)
                                    {
                                        $var = 1;
                                        return $var;
                                    }
                EOD."\n                ",
        ];

        yield 'not in function scope' => [
            <<<'EOD'
                <?php
                                    $a = 1; // var might be global here
                                    return $a;
                EOD."\n                ",
        ];

        yield 'open-close with ;' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        $a = 1;
                                        ?>
                                        <?php
                                        ;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'open-close single line' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        $a = 1 ?><?php return $a;
                                    }
                EOD,
        ];

        yield 'open-close' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        $a = 1
                                        ?>
                                        <?php
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'open-close before function' => [
            <<<'EOD'
                <?php
                                    $zz = 1 ?><?php
                                    function a($zz)
                                    {
                                        ;
                                        return $zz;
                                    }
                EOD."\n                ",
        ];

        yield 'return complex statement' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        $a = 1;
                                        return $a + $c;
                                    }
                EOD."\n                ",
        ];

        yield 'array assign' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        $_SERVER["abc"] = 3;
                                        return $_SERVER;
                                    }
                EOD."\n                ",
        ];

        yield 'if assign' => [
            <<<'EOD'
                <?php
                                    function foo ($bar)
                                    {
                                        $a = 123;
                                        if ($bar)
                                            $a = 12345;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'else assign' => [
            <<<'EOD'
                <?php
                                    function foo ($bar)
                                    {
                                        $a = 123;
                                        if ($bar)
                                            ;
                                        else
                                            $a = 12345;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'elseif assign' => [
            <<<'EOD'
                <?php
                                    function foo ($bar)
                                    {
                                        $a = 123;
                                        if ($bar)
                                            ;
                                        elseif($b)
                                            $a = 12345;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'echo $a = N / comment $a = N;' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        $a = 1;
                                        echo $a."=1";
                                        return $a;
                                    }

                                    function b($c)
                                    {
                                        $a = 1;
                                        echo $a."=1;";
                                        return $a;
                                    }

                                    function c($c)
                                    {
                                        $a = 1;
                                        echo $a;
                                        // $a =1;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'if ($a = N)' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        if ($a = 1)
                                            return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'changed after declaration' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        $a = 1;
                                        $a += 1;
                                        return $a;
                                    }

                                    function b($c)
                                    {
                                        $a = 1;
                                        $a -= 1;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'complex statement' => [
            <<<'EOD'
                <?php
                                    function a($c)
                                    {
                                        $d = $c && $a = 1;
                                        return $a;
                                    }
                EOD."\n                ",
        ];

        yield 'PHP close tag within function' => [
            <<<'EOD'
                <?php
                                    function a($zz)
                                    {
                                        $zz = 1 ?><?php
                                        ;
                                        return $zz;
                                    }
                EOD."\n                ",
        ];

        yield 'import global using "require"' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        require __DIR__."/test3.php";
                                        $b = 1;
                                        return $b;
                                    }
                EOD."\n                ",
        ];

        yield 'import global using "require_once"' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        require_once __DIR__."/test3.php";
                                        $b = 1;
                                        return $b;
                                    }
                EOD."\n                ",
        ];

        yield 'import global using "include"' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        include __DIR__."/test3.php";
                                        $b = 1;
                                        return $b;
                                    }
                EOD."\n                ",
        ];

        yield 'import global using "include_once"' => [
            <<<'EOD'
                <?php
                                    function a()
                                    {
                                        include_once __DIR__."/test3.php";
                                        $b = 1;
                                        return $b;
                                    }
                EOD."\n                ",
        ];

        yield 'eval' => [
            <<<'EOD'
                <?php
                                    $b = function ($z) {
                                        $c = eval($z);

                                        return $c;
                                    };

                                    $c = function ($x) {
                                        $x = eval($x);
                                        $x = 1;
                                        return $x;
                                    };
                EOD."\n                ",
        ];

        yield '${X}' => [
            <<<'EOD'
                <?php
                                    function A($g)
                                    {
                                        $h = ${$g};

                                        return $h;
                                    }
                EOD."\n                ",
        ];

        yield '$$' => [
            <<<'EOD'
                <?php
                                    function B($c)
                                    {
                                        $b = $$c;

                                        return $b;
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                class XYZ
                {
                    public function test1()
                    {
                        $GLOBALS['a'] = 2;

                        return $GLOBALS;
                    }

                    public function test2()
                    {
                        $_server = 2;

                        return $_server;
                    }

                    public function __destruct()
                    {
                        $GLOBALS['a'] = 2;

                        return $GLOBALS['a']; // destruct cannot return but still lints
                    }
                };

                $a = new XYZ();
                $a = 1;
                var_dump($a); // $a = 2 here _╯°□°╯︵┻━┻

                EOD,
        ];

        yield 'variable returned by reference in function' => [
            <<<'EOD'
                <?php
                                function &foo() {
                                    $var = 1;
                                    return $var;
                                }
                EOD,
        ];

        yield 'variable returned by reference in method' => [
            <<<'EOD'
                <?php
                                class Foo {
                                    public function &bar() {
                                        $var = 1;
                                        return $var;
                                    }
                                }
                EOD,
        ];

        yield 'variable returned by reference in lambda' => [
            '<?php $a = function &() {$z = new A(); return $z;};',
        ];

        yield [
            <<<'EOD'
                <?php
                                function F() {
                                    $a = 1;

                                    while(bar()) {
                                        ++$a;
                                    }; // keep this

                                    return $a;
                                }
                EOD."\n                ",
        ];

        yield 'try/catch/finally' => [
            <<<'EOD'
                <?php
                                function add($a, $b): mixed
                                {
                                    try {
                                        $result = $a + $b;

                                        return $result;
                                    } catch (\Throwable $th) {
                                        throw $th;
                                    } finally {
                                        echo 'result:', $result, \PHP_EOL;
                                    }
                                }
                EOD."\n                ",
        ];

        yield 'try with multiple catch blocks' => [
            <<<'EOD'
                <?php
                                function foo() {
                                    try {
                                        $bar = bar();

                                        return $bar;
                                    } catch (\LogicException $e) {
                                        echo "catch ... ";
                                    } catch (\RuntimeException $e) {
                                        echo $bar;
                                    }
                                }
                EOD,
        ];

        yield 'try/catch/finally with some comments' => [
            <<<'EOD'
                <?php
                                function add($a, $b): mixed
                                {
                                    try {
                                        $result = $a + $b;

                                        return $result;
                                    } /* foo */ catch /** bar */ (\LogicException $th) {
                                        throw $th;
                                    }
                                    // Or maybe this....
                                    catch (\RuntimeException $th) {
                                        throw $th;
                                    }
                                    # Print the result anyway
                                    finally {
                                        echo 'result:', $result, \PHP_EOL;
                                    }
                                }
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideDoNotFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testDoNotFix80(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideDoNotFix80Cases(): iterable
    {
        yield 'try with non-capturing catch block' => [
            <<<'EOD'
                <?php
                                function add($a, $b): mixed
                                {
                                    try {
                                        $result = $a + $b;

                                        return $result;
                                    }
                                    catch (\Throwable) {
                                        noop();
                                    }
                                    finally {
                                        echo 'result:', $result, \PHP_EOL;
                                    }
                                }
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideRepetitiveFixCases
     */
    public function testRepetitiveFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideRepetitiveFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                function foo() {
                    return bar();
                }

                EOD,
            <<<'EOD'
                <?php

                function foo() {
                    $a = bar();
                    $b = $a;

                    return $b;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                function foo(&$c) {
                    $a = $c;
                    $b = $a;

                    return $b;
                }

                EOD,
        ];

        $expected = "<?php\n";
        $input = "<?php\n";

        for ($i = 0; $i < 10; ++$i) {
            $expected .= sprintf("\nfunction foo%d() {\n\treturn bar();\n}", $i);
            $input .= sprintf("\nfunction foo%d() {\n\t\$a = bar();\n\t\$b = \$a;\n\nreturn \$b;\n}", $i);
        }

        yield [$expected, $input];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'match' => [
            <<<'EOD'
                <?php
                            function Foo($food) {
                                return match ($food) {
                                    "apple" => "This food is an apple",
                                    "cake" => "This food is a cake",
                                };
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php
                            function Foo($food) {
                                $return_value = match ($food) {
                                    "apple" => "This food is an apple",
                                    "cake" => "This food is a cake",
                                };

                                return $return_value;
                            }
                EOD."\n            ",
        ];

        yield 'attribute before anonymous `class`' => [
            <<<'EOD'
                <?php
                                function A()
                                {
                                    return new #[Foo] class {};
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function A()
                                {
                                    $a = new #[Foo] class {};
                                    return $a;
                                }
                EOD."\n            ",
        ];
    }

    /**
     * @requires PHP 8.3
     *
     * @dataProvider provideFixPhp83Cases
     */
    public function testFixPhp83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPhp83Cases(): iterable
    {
        yield 'anonymous readonly class' => [
            <<<'EOD'
                <?php
                                function A()
                                {
                                    return new readonly class {};
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function A()
                                {
                                    $a = new readonly class {};
                                    return $a;
                                }
                EOD."\n            ",
        ];

        yield 'attribute before anonymous `readonly class`' => [
            <<<'EOD'
                <?php
                                function A()
                                {
                                    return new #[Foo] readonly class {};
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function A()
                                {
                                    $a = new #[Foo] readonly class {};
                                    return $a;
                                }
                EOD."\n            ",
        ];
    }
}
