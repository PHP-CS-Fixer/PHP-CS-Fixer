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
 * @author Varga Bence <vbence@czentral.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer
 */
final class NoSpacesAfterFunctionNameFixerTest extends AbstractFixerTestCase
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
        return array(
            'test function call' => array(
                '<?php abc($a);',
                '<?php abc ($a);',
            ),
            'test method call' => array(
                '<?php $o->abc($a);',
                '<?php $o->abc ($a);',
            ),
            'test function-like constructs' => array(
                '<?php
    include("something.php");
    include_once("something.php");
    require("something.php");
    require_once("something.php");
    print("hello");
    unset($hello);
    isset($hello);
    empty($hello);
    die($hello);
    echo("hello");
    array("hello");
    list($a, $b) = $c;
    eval("a");
    foo();
    $foo = &ref();
    ',
                '<?php
    include ("something.php");
    include_once ("something.php");
    require ("something.php");
    require_once ("something.php");
    print ("hello");
    unset ($hello);
    isset ($hello);
    empty ($hello);
    die ($hello);
    echo ("hello");
    array ("hello");
    list ($a, $b) = $c;
    eval ("a");
    foo ();
    $foo = &ref ();
    ',
            ),
            array(
                '<?php echo foo(1) ? "y" : "n";',
                '<?php echo foo (1) ? "y" : "n";',
            ),
            array(
                '<?php echo isset($name) ? "y" : "n";',
                '<?php echo isset ($name) ? "y" : "n";',
            ),
            array(
                '<?php include (isHtml())? "1.html": "1.php";',
                '<?php include (isHtml ())? "1.html": "1.php";',
            ),
            // skip other language constructs
            array(
                '<?php $a = 2 * (1 + 1);',
            ),
            array(
                '<?php echo ($a == $b) ? "foo" : "bar";',
            ),
            array(
                '<?php echo ($a == test($b)) ? "foo" : "bar";',
            ),
            array(
                '<?php include ($html)? "custom.html": "custom.php";',
            ),
            'don\'t touch function declarations' => array(
                '<?php
                function TisMy ($p1)
                {
                    print $p1;
                }
                ',
            ),
            array(
                '<?php class A {
                    function TisMy    ($p1)
                    {
                        print $p1;
                    }
                }',
            ),
            'test dynamic by array' => array(
                '<?php $a["e"](1); $a{2}(1);',
                '<?php $a["e"] (1); $a{2} (1);',
            ),
            'test variable variable' => array(
                '<?php
${$e}(1);
$$e(2);
                ',
                "<?php
\${\$e}\t(1);
\$\$e    (2);
                ",
            ),
            'test dynamic function and method calls' => array(
                '<?php $b->$a(); $c();',
                '<?php $b->$a  (); $c  ();',
            ),
            'test function call comment' => array(
                '<?php abc#
 ($a);',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix54Cases
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix54Cases()
    {
        return array(
            array(
                '<?php echo (new Process())->getOutput();',
                '<?php echo (new Process())->getOutput ();',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return array(
            array(
                '<?php $a()(1);',
                '<?php $a () (1);',
            ),
        );
    }
}
