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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Denis Sokolov <denis@sokolov.cc>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FunctionDeclarationFixerTest extends AbstractFixerTestCase
{
    private static $configurationClosureSpacingNone = array('closure_function_spacing' => 'none');

    public function testInvalidConfigurationClosureFunctionSpacing()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[function_declaration\] Spacing is invalid. Should be one of: "none", "one".$#'
        );

        $this->fixer->configure(array('closure_function_spacing' => 'neither'));
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null, array $configuration = null)
    {
        if (isset($configuration)) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                // non-PHP test
                'function foo () {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function	foo() {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo	() {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo () {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function
foo () {}',
            ),
            array(
                '<?php function ($i) {};',
                '<?php function($i) {};',
            ),
            array(
                '<?php function _function() {}',
                '<?php function _function () {}',
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true){}',
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true)    {}',
            ),
            array(
                '<?php function foo($a)
{}',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ($b)     {};',
            ),
            array(
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo) use($bar, $baz) {};',
            ),
            array(
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use ($bar, $baz) {};',
            ),
            array(
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use($bar, $baz) {};',
            ),
            array(
                '<?php function &foo($a) {}',
                '<?php function &foo( $a ) {}',
            ),
            array(
                '<?php function foo($a)
	{}',
                '<?php function foo( $a)
	{}',
            ),
            array(
                '<?php
    function foo(
        $a,
        $b,
        $c
    ) {}',
            ),
            array(
                '<?php $function = function () {};',
                '<?php $function = function(){};',
            ),
            array(
                '<?php $function("");',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function($a)use($b) {};',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function($a)         use      ($b) {};',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ( $b ) {};',
            ),
            array(
                '<?php function &($a) use ($b) {};',
                '<?php function &(  $a   ) use (   $b      ) {};',
            ),
            array(
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
            ),
            // do not remove multiline space before { when end of previous line is a comment
            array(
                '<?php
function foo() // bar
{              // baz
}',
            ),
            array(
                '<?php
function foo() /* bar */
{              /* baz */
}',
            ),
            array(
                // non-PHP test
                'function foo () {}',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo() {}',
                '<?php function	foo() {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo () {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo	() {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo() {}',
                '<?php function
foo () {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function($i) {};',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function _function() {}',
                '<?php function _function () {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true){}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true)    {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo($a)
{}',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function($a) use ($b) {};',
                '<?php function ($a) use ($b)     {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo) use($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use ($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function &foo($a) {}',
                '<?php function &foo( $a ) {}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function foo($a)
	{}',
                '<?php function foo( $a)
	{}',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php
    function foo(
        $a,
        $b,
        $c
    ) {}',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php $function = function() {};',
                '<?php $function = function (){};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php $function("");',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function($a) use ($b) {};',
                '<?php function ($a)use($b) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function($a) use ($b) {};',
                '<?php function ($a)         use      ($b) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function($a) use ($b) {};',
                '<?php function ($a) use ( $b ) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php function&($a) use ($b) {};',
                '<?php function &(  $a   ) use (   $b      ) {};',
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
                null,
                self::$configurationClosureSpacingNone,
            ),
            // do not remove multiline space before { when end of previous line is a comment
            array(
                '<?php
function foo() // bar
{              // baz
}',
                null,
                self::$configurationClosureSpacingNone,
            ),
            array(
                '<?php
function foo() /* bar */
{              /* baz */
}',
                null,
                self::$configurationClosureSpacingNone,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provide54Cases
     * @requires PHP 5.4
     */
    public function test54($expected, $input = null, array $configuration = null)
    {
        if (isset($configuration)) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provide54Cases()
    {
        return array(
            array(
                '<?php
                    $b = static function ($a) {
                        echo $a;
                    };
                ',
                '<?php
                    $b = static     function( $a )   {
                        echo $a;
                    };
                ',
            ),
            array(
                '<?php
                    $b = static function($a) {
                        echo $a;
                    };
                ',
                '<?php
                    $b = static     function ( $a )   {
                        echo $a;
                    };
                ',
                self::$configurationClosureSpacingNone,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function test70($expected, $input = null, array $configuration = null)
    {
        if (isset($configuration)) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provide70Cases()
    {
        return array(
            array('<?php use function Foo\bar; bar ( 1 );'),
            array('<?php use function some\test\{fn_a, fn_b, fn_c};'),
            array('<?php use function some\test\{fn_a, fn_b, fn_c} ?>'),
            array('<?php use function Foo\bar; bar ( 1 );', null, self::$configurationClosureSpacingNone),
            array('<?php use function some\test\{fn_a, fn_b, fn_c};', null, self::$configurationClosureSpacingNone),
            array('<?php use function some\test\{fn_a, fn_b, fn_c} ?>', null, self::$configurationClosureSpacingNone),
        );
    }
}
