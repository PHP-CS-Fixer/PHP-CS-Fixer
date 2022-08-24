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

    public function provideFixNestedFunctionsCases(): array
    {
        return [
            [
                '<?php
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
',
                '<?php
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
',
            ],
        ];
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
                    function A()
                    {
                        return 15;
                    }
                ',
                '<?php
                    function A()
                    {
                        $a = 15;
                        return $a;
                    }
                ',
            ],
            [
                '<?php
                    function A()
                    {
                        /*0*/return /*1*//*2*/15;/*3*//*4*/ /*5*/ /*6*//*7*//*8*/
                    }
                ',
                '<?php
                    function A()
                    {
                        /*0*/$a/*1*/=/*2*/15;/*3*//*4*/ /*5*/ return/*6*/$a/*7*/;/*8*/
                    }
                ',
            ],
            'comments with leading space' => [
                '<?php
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
                ',
                '<?php
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
                ',
            ],
            [
                '<?php
                    abstract class B
                    {
                        abstract protected function Z();public function A()
                        {
                            return 16;
                        }
                    }
                ',
                '<?php
                    abstract class B
                    {
                        abstract protected function Z();public function A()
                        {
                            $a = 16; return $a;
                        }
                    }
                ',
            ],
            [
                '<?php
                    function b() {
                        if ($c) {
                            return 0;
                        }
                        return testFunction(654+1);
                    }
                ',
                '<?php
                    function b() {
                        if ($c) {
                            $b = 0;
                            return $b;
                        }
                        $a = testFunction(654+1);
                        return $a;
                    }
                ',
            ],
            'minimal notation' => [
                '<?php $e=function(){return 1;};$f=function(){return 1;};$g=function(){return 1;};',
                '<?php $e=function(){$a=1;return$a;};$f=function(){$a=1;return$a;};$g=function(){$a=1;return$a;};',
            ],
            [
                '<?php
                    function A()
                    {#1
#2                    '.'
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
                ',
                '<?php
                    function A()
                    {#1
#2                    '.'
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
                ',
            ],
            [
                '<?php
function A($b)
{
        // Comment
        return a("2", 4, $b);
}
',
                '<?php
function A($b)
{
        // Comment
        $value = a("2", 4, $b);

        return $value;
}
',
            ],
            [
                '<?php function a($b,$c)  {if($c>1){echo 1;} return (1 + 2 + $b);  }',
                '<?php function a($b,$c)  {if($c>1){echo 1;} $a= (1 + 2 + $b);return $a; }',
            ],
            [
                '<?php function a($b,$c)  {return (3 * 4 + $b);  }',
                '<?php function a($b,$c)  {$zz= (3 * 4 + $b);return $zz; }',
            ],
            [
                '<?php
                    function a() {
                        return 4563;
                          ?> <?php
                    }
                ',
                '<?php
                    function a() {
                        $a = 4563;
                        return $a ?> <?php
                    }
                ',
            ],
            [
                '<?php
                    function a()
                    {
                        return $c + 1; /*
var names are case-insensitive */ }
                ',
                '<?php
                    function a()
                    {
                        $A = $c + 1; /*
var names are case-insensitive */ return $a   ;}
                ',
            ],
            [
                '<?php
                    function A()
                    {
                        return $f[1]->a();
                    }
                ',
                '<?php
                    function A()
                    {
                        $a = $f[1]->a();
                        return $a;
                    }
                ',
                [
                    '<?php
                    function a($foos) {
                        return array_map(function ($foo) {
                            return (string) $foo;
                        }, $foos);
                    }',
                    '<?php
                    function a($foos) {
                        $bars = array_map(function ($foo) {
                            return (string) $foo;
                        }, $foos);

                        return $bars;
                    }',
                ],
                [
                    '<?php
                    function a($foos) {
                        return ($foos = [\'bar\']);
                    }',
                    '<?php
                    function a($foos) {
                        $bars = ($foos = [\'bar\']);

                        return $bars;
                    }',
                ],
            ],
            [
                '<?php
                    function a($foos) {
                        return (function ($foos) {
                            return $foos;
                        })($foos);
                    }',
                '<?php
                    function a($foos) {
                        $bars = (function ($foos) {
                            return $foos;
                        })($foos);

                        return $bars;
                    }',
            ],
            'anonymous classes' => [
                '<?php
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
                        return new class extends Y implements O,P {};
                    }
                ',
                '<?php
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
                        $c = new class extends Y implements O,P {};
                        return $c;
                    }
                ',
            ],
            'lambda' => [
                '<?php
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
                ',
                '<?php
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
                ',
            ],
            'arrow functions' => [
                '<?php
                    function Foo() {
                        return fn($x) => $x + $y;
                    }
                ',
                '<?php
                    function Foo() {
                        $fn1 = fn($x) => $x + $y;

                        return $fn1;
                    }
                ',
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
            'invalid reference stays invalid' => [
                '<?php
                    function bar() {
                        $foo = &foo();
                        return $foo;
                }',
            ],
            'static' => [
                '<?php
                    function a() {
                        static $a;
                        $a = time();
                        return $a;
                    }
                ',
            ],
            'global' => [
                '<?php
                    function a() {
                        global $a;
                        $a = time();
                        return $a;
                    }
                ',
            ],
            'passed by reference' => [
                '<?php
                function foo(&$var)
                    {
                        $var = 1;
                        return $var;
                    }
                ',
            ],
            'not in function scope' => [
                '<?php
                    $a = 1; // var might be global here
                    return $a;
                ',
            ],
            'open-close with ;' => [
                '<?php
                    function a()
                    {
                        $a = 1;
                        ?>
                        <?php
                        ;
                        return $a;
                    }
                ',
            ],
            'open-close single line' => [
                '<?php
                    function a()
                    {
                        $a = 1 ?><?php return $a;
                    }',
            ],
            'open-close' => [
                '<?php
                    function a()
                    {
                        $a = 1
                        ?>
                        <?php
                        return $a;
                    }
                ',
            ],
            'open-close before function' => [
                '<?php
                    $zz = 1 ?><?php
                    function a($zz)
                    {
                        ;
                        return $zz;
                    }
                ',
            ],
            'return complex statement' => [
                '<?php
                    function a($c)
                    {
                        $a = 1;
                        return $a + $c;
                    }
                ',
            ],
            'array assign' => [
                '<?php
                    function a($c)
                    {
                        $_SERVER["abc"] = 3;
                        return $_SERVER;
                    }
                ',
            ],
            'if assign' => [
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            $a = 12345;
                        return $a;
                    }
                ',
            ],
            'else assign' => [
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            ;
                        else
                            $a = 12345;
                        return $a;
                    }
                ',
            ],
            'elseif assign' => [
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            ;
                        elseif($b)
                            $a = 12345;
                        return $a;
                    }
                ',
            ],
            'echo $a = N / comment $a = N;' => [
                '<?php
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
                ',
            ],
            'if ($a = N)' => [
                '<?php
                    function a($c)
                    {
                        if ($a = 1)
                            return $a;
                    }
                ',
            ],
            'changed after declaration' => [
                '<?php
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
                ',
            ],
            'complex statement' => [
                '<?php
                    function a($c)
                    {
                        $d = $c && $a = 1;
                        return $a;
                    }
                ',
            ],
            'PHP close tag within function' => [
                '<?php
                    function a($zz)
                    {
                        $zz = 1 ?><?php
                        ;
                        return $zz;
                    }
                ',
            ],
            'import global using "require"' => [
                '<?php
                    function a()
                    {
                        require __DIR__."/test3.php";
                        $b = 1;
                        return $b;
                    }
                ',
            ],
            'import global using "require_once"' => [
                '<?php
                    function a()
                    {
                        require_once __DIR__."/test3.php";
                        $b = 1;
                        return $b;
                    }
                ',
            ],
            'import global using "include"' => [
                '<?php
                    function a()
                    {
                        include __DIR__."/test3.php";
                        $b = 1;
                        return $b;
                    }
                ',
            ],
            'import global using "include_once"' => [
                '<?php
                    function a()
                    {
                        include_once __DIR__."/test3.php";
                        $b = 1;
                        return $b;
                    }
                ',
            ],
            'eval' => [
                '<?php
                    $b = function ($z) {
                        $c = eval($z);

                        return $c;
                    };

                    $c = function ($x) {
                        $x = eval($x);
                        $x = 1;
                        return $x;
                    };
                ',
            ],
            '${X}' => [
                '<?php
                    function A($g)
                    {
                        $h = ${$g};

                        return $h;
                    }
                ',
            ],
            '$$' => [
                '<?php
                    function B($c)
                    {
                        $b = $$c;

                        return $b;
                    }
                ',
            ],
            [
                '<?php
class XYZ
{
    public function test1()
    {
        $GLOBALS[\'a\'] = 2;

        return $GLOBALS;
    }

    public function test2()
    {
        $_server = 2;

        return $_server;
    }

    public function __destruct()
    {
        $GLOBALS[\'a\'] = 2;

        return $GLOBALS[\'a\']; // destruct cannot return but still lints
    }
};

$a = new XYZ();
$a = 1;
var_dump($a); // $a = 2 here _╯°□°╯︵┻━┻
',
            ],
            'variable returned by reference in function' => [
                '<?php
                function &foo() {
                    $var = 1;
                    return $var;
                }',
            ],
            'variable returned by reference in method' => [
                '<?php
                class Foo {
                    public function &bar() {
                        $var = 1;
                        return $var;
                    }
                }',
            ],
            'variable returned by reference in lambda' => [
                '<?php $a = function &() {$z = new A(); return $z;};',
            ],
            [
                '<?php
                function F() {
                    $a = 1;

                    while(bar()) {
                        ++$a;
                    }; // keep this

                    return $a;
                }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideRepetitiveFixCases
     */
    public function testRepetitiveFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideRepetitiveFixCases(): iterable
    {
        yield [
            '<?php

function foo() {
    return bar();
}
',
            '<?php

function foo() {
    $a = bar();
    $b = $a;

    return $b;
}
',
        ];

        yield [
            '<?php

function foo(&$c) {
    $a = $c;
    $b = $a;

    return $b;
}
',
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
     * @dataProvider providePhp80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function providePhp80Cases(): iterable
    {
        yield 'match' => [
            '<?php
            function Foo($food) {
                return match ($food) {
                    "apple" => "This food is an apple",
                    "cake" => "This food is a cake",
                };
            }
            ',
            '<?php
            function Foo($food) {
                $return_value = match ($food) {
                    "apple" => "This food is an apple",
                    "cake" => "This food is a cake",
                };

                return $return_value;
            }
            ',
        ];
    }
}
