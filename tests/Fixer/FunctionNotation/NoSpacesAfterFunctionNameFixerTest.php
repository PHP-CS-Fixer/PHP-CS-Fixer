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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer>
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer
 *
 * @author Varga Bence <vbence@czentral.org>
 */
final class NoSpacesAfterFunctionNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'test function call' => [
            '<?php abc($a);',
            '<?php abc ($a);',
        ];

        yield 'test method call' => [
            '<?php $o->abc($a);',
            '<?php $o->abc ($a);',
        ];

        yield 'test function-like constructs' => [
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
        ];

        yield [
            '<?php echo foo(1) ? "y" : "n";',
            '<?php echo foo (1) ? "y" : "n";',
        ];

        yield [
            '<?php echo isset($name) ? "y" : "n";',
            '<?php echo isset ($name) ? "y" : "n";',
        ];

        yield [
            '<?php include (isHtml())? "1.html": "1.php";',
            '<?php include (isHtml ())? "1.html": "1.php";',
        ];

        // skip other language constructs
        yield [
            '<?php $a = 2 * (1 + 1);',
        ];

        yield [
            '<?php echo ($a == $b) ? "foo" : "bar";',
        ];

        yield [
            '<?php echo ($a == test($b)) ? "foo" : "bar";',
        ];

        yield [
            '<?php include ($html)? "custom.html": "custom.php";',
        ];

        yield 'don\'t touch function declarations' => [
            '<?php
                function TisMy ($p1)
                {
                    print $p1;
                }
                ',
        ];

        yield [
            '<?php class A {
                    function TisMy    ($p1)
                    {
                        print $p1;
                    }
                }',
        ];

        yield 'test dynamic by array' => [
            '<?php $a["e"](1); $a[2](1);',
            '<?php $a["e"] (1); $a[2] (1);',
        ];

        yield 'test variable variable' => [
            '<?php
${$e}(1);
$$e(2);
                ',
            "<?php
\${\$e}\t(1);
\$\$e    (2);
                ",
        ];

        yield 'test dynamic function and method calls' => [
            '<?php $b->$a(); $c();',
            '<?php $b->$a  (); $c  ();',
        ];

        yield 'test function call comment' => [
            '<?php abc#
 ($a);',
        ];

        yield [
            '<?php echo (new Process())->getOutput();',
            '<?php echo (new Process())->getOutput ();',
        ];

        yield [
            '<?php $a()(1);',
            '<?php $a () (1);',
        ];

        yield [
            '<?php
                echo (function () {})();
                echo ($propertyValue ? "TRUE" : "FALSE") . EOL;
                echo(FUNCTION_1);
                echo (EXPRESSION + CONST_1), CONST_2;
            ',
            '<?php
                echo (function () {})();
                echo ($propertyValue ? "TRUE" : "FALSE") . EOL;
                echo (FUNCTION_1);
                echo (EXPRESSION + CONST_1), CONST_2;
            ',
        ];

        yield [
            '<?php
                include(SOME_PATH_1);
                include_once(SOME_PATH_2);
                require(SOME_PATH_3);
                require_once(SOME_PATH_4);
                print(SOME_VALUE);
                print(FIRST_HALF_OF_STRING_1 . SECOND_HALF_OF_STRING_1);
                print((FIRST_HALF_OF_STRING_2) . (SECOND_HALF_OF_STRING_2));
            ',
            '<?php
                include         (SOME_PATH_1);
                include_once    (SOME_PATH_2);
                require         (SOME_PATH_3);
                require_once    (SOME_PATH_4);
                print           (SOME_VALUE);
                print           (FIRST_HALF_OF_STRING_1 . SECOND_HALF_OF_STRING_1);
                print           ((FIRST_HALF_OF_STRING_2) . (SECOND_HALF_OF_STRING_2));
            ',
        ];

        yield [
            '<?php
                include         (DIR) . FILENAME_1;
                include_once    (DIR) . (FILENAME_2);
                require         (DIR) . FILENAME_3;
                require_once    (DIR) . (FILENAME_4);
                print           (FIRST_HALF_OF_STRING_1) . SECOND_HALF_OF_STRING_1;
                print           (FIRST_HALF_OF_STRING_2) . ((((SECOND_HALF_OF_STRING_2))));
                print           ((FIRST_HALF_OF_STRING_3)) . ((SECOND_HALF_OF_STRING_3));
                print           ((((FIRST_HALF_OF_STRING_4)))) . ((((SECOND_HALF_OF_STRING_4))));
            ',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'test dynamic by array, curly mix' => [
            '<?php $a["e"](1); $a{2}(1);',
            '<?php $a["e"] (1); $a{2} (1);',
        ];

        yield 'test dynamic by array, curly only' => [
            '<?php $a{"e"}(1); $a{2}(1);',
            '<?php $a{"e"} (1); $a{2} (1);',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php strlen(...);',
            '<?php strlen  (...);',
        ];
    }
}
