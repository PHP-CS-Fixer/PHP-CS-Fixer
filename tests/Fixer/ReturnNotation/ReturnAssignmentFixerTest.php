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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer
 */
final class ReturnAssignmentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixNestedFunctionsCases
     *
     * @param string $expected
     * @param string $input
     */
    public function testFixNestedFunctions($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixNestedFunctionsCases()
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
     *
     * @param string $expected
     * @param string $input
     */
    public function testFix($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
var names are case insensitive */ }
                ',
                '<?php
                    function a()
                    {
                        $A = $c + 1; /*
var names are case insensitive */ return $a   ;}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideDoNotFixCases
     *
     * @param string $expected
     */
    public function testDoNotFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideDoNotFixCases()
    {
        return [
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
            [
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
            [
                '<?php
                    function a()
                    {
                        $a = 1 ?><?php return $a;
                    }',
            ],
            [
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
            [
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
        $GLOBALS = 2;

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
        ];
    }
}
