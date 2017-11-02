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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer
 */
final class NoUnreachableDefaultArgumentValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return array(
            array(
                '<?php function bFunction($foo, $bar) {}',
                '<?php function bFunction($foo = null, $bar) {}',
            ),
            array(
                '<?php function bFunction($foo, $bar) {}',
                '<?php function bFunction($foo = "two words", $bar) {}',
            ),
            array(
                '<?php function cFunction($foo, $bar, $baz) {}',
                '<?php function cFunction($foo = false, $bar = "bar", $baz) {}',
            ),
            array(
                '<?php function dFunction($foo, $bar, $baz) {}',
                '<?php function dFunction($foo = false, $bar, $baz) {}',
            ),
            array(
                '<?php function foo (Foo $bar = null, $baz) {}',
            ),
            array(
                '<?php function eFunction($foo, $bar, \SplFileInfo $baz, $x = "default") {}',
                '<?php function eFunction($foo, $bar = "removedDefault", \SplFileInfo $baz, $x = "default") {}',
            ),
            array(
                <<<'EOT'
                    <?php
                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};

                        function fFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};
EOT
                ,
                <<<'EOT'
                    <?php
                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};

                        function fFunction($foo, $bar = 'removedValue', \SplFileInfo $baz, $x = 'default') {};
EOT
            ),
            array(
                '<?php function foo ($bar /* a */  /* b */ , $c) {}',
                '<?php function foo ($bar /* a */ = /* b */ 1, $c) {}',
            ),
            array(
                '<?php function hFunction($foo,$bar,\SplFileInfo $baz,$x = 5) {};',
                '<?php function hFunction($foo,$bar="removedValue",\SplFileInfo $baz,$x = 5) {};',
            ),
            array(
                '<?php function eFunction($foo, $bar, \SplFileInfo $baz = null, $x) {}',
                '<?php function eFunction($foo = PHP_EOL, $bar, \SplFileInfo $baz = null, $x) {}',
            ),
            array(
                '<?php function eFunction($foo, $bar) {}',
                '<?php function eFunction($foo       = null, $bar) {}',
            ),
            array(
                <<<'EOT'
                    <?php
                        function foo(
                            $a, // test
                            $b, /* test */
                            $c, // abc
                            $d
                        ) {}
EOT
                ,
                <<<'EOT'
                    <?php
                        function foo(
                            $a = 1, // test
                            $b = 2, /* test */
                            $c = null, // abc
                            $d
                        ) {}
EOT
            ),
            array(
                '<?php function foo($foo, $bar) {}',
                '<?php function foo($foo = array(array(1)), $bar) {}',
            ),
            array(
                '<?php function a($a, $b) {}',
                '<?php function a($a = array("a" => "b", "c" => "d"), $b) {}',
            ),
            array(
                '<?php function a($a, $b) {}',
                '<?php function a($a = ["a" => "b", "c" => "d"], $b) {}',
            ),
            array(
                '<?php function a($a, $b) {}',
                '<?php function a($a = NULL, $b) {}',
            ),
            array(
                '<?php function a(\SplFileInfo $a = Null, $b) {}',
            ),
            array(
                '<?php function a(array $a = null, $b) {}',
            ),
            array(
                '<?php function a(callable $a = null, $b) {}',
            ),
            array(
                '<?php function a(\SplFileInfo &$a = Null, $b) {}',
            ),
            array(
                '<?php function a(&$a, $b) {}',
                '<?php function a(&$a = null, $b) {}',
            ),
            array(
                '<?php $fnc = function ($a, $b = 1) use ($c) {};',
            ),
            array(
                '<?php $fnc = function ($a, $b) use ($c) {};',
                '<?php $fnc = function ($a = 1, $b) use ($c) {};',
            ),
            array(
                '<?php function bFunction($foo#
 #
 #
 ,#
$bar) {}',
                '<?php function bFunction($foo#
 =#
 null#
 ,#
$bar) {}',
            ),
        );
    }

    /**
     * @dataProvider provideFix56Cases
     * @requires PHP 5.6
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFix56Cases()
    {
        return array(
            array(
                '<?php function a($a = 1, ...$b) {}',
            ),
            array(
                '<?php function a($a = 1, \SplFileInfo ...$b) {}',
            ),
        );
    }
}
