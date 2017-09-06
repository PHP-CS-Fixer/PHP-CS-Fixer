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
 * @author Denis Sokolov <denis@sokolov.cc>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer
 */
final class FunctionDeclarationFixerTest extends AbstractFixerTestCase
{
    private static $configurationClosureSpacingNone = ['closure_function_spacing' => 'none'];

    public function testInvalidConfigurationClosureFunctionSpacing()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '#^\[function_declaration\] Invalid configuration: The option "closure_function_spacing" with value "neither" is invalid\. Accepted values are: "none", "one"\.$#'
        );

        $this->fixer->configure(['closure_function_spacing' => 'neither']);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                // non-PHP test
                'function foo () {}',
            ],
            [
                '<?php function foo() {}',
                '<?php function	foo() {}',
            ],
            [
                '<?php function foo() {}',
                '<?php function foo	() {}',
            ],
            [
                '<?php function foo() {}',
                '<?php function foo () {}',
            ],
            [
                '<?php function foo() {}',
                '<?php function
foo () {}',
            ],
            [
                '<?php function ($i) {};',
                '<?php function($i) {};',
            ],
            [
                '<?php function _function() {}',
                '<?php function _function () {}',
            ],
            [
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true){}',
            ],
            [
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true)    {}',
            ],
            [
                '<?php function foo($a)
{}',
            ],
            [
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ($b)     {};',
            ],
            [
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo) use($bar, $baz) {};',
            ],
            [
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use ($bar, $baz) {};',
            ],
            [
                '<?php $foo = function ($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use($bar, $baz) {};',
            ],
            [
                '<?php function &foo($a) {}',
                '<?php function &foo( $a ) {}',
            ],
            [
                '<?php function foo($a)
	{}',
                '<?php function foo( $a)
	{}',
            ],
            [
                '<?php
    function foo(
        $a,
        $b,
        $c
    ) {}',
            ],
            [
                '<?php $function = function () {};',
                '<?php $function = function(){};',
            ],
            [
                '<?php $function("");',
            ],
            [
                '<?php function ($a) use ($b) {};',
                '<?php function($a)use($b) {};',
            ],
            [
                '<?php function ($a) use ($b) {};',
                '<?php function($a)         use      ($b) {};',
            ],
            [
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ( $b ) {};',
            ],
            [
                '<?php function &($a) use ($b) {};',
                '<?php function &(  $a   ) use (   $b      ) {};',
            ],
            [
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
            ],
            // do not remove multiline space before { when end of previous line is a comment
            [
                '<?php
function foo() // bar
{              // baz
}',
            ],
            [
                '<?php
function foo() /* bar */
{              /* baz */
}',
            ],
            [
                // non-PHP test
                'function foo () {}',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo() {}',
                '<?php function	foo() {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo() {}',
                '<?php function foo () {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo() {}',
                '<?php function foo	() {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo() {}',
                '<?php function
foo () {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function($i) {};',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function _function() {}',
                '<?php function _function () {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true){}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true)    {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo($a)
{}',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function($a) use ($b) {};',
                '<?php function ($a) use ($b)     {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo) use($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use ($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php $foo = function($foo) use ($bar, $baz) {};',
                '<?php $foo = function ($foo)use($bar, $baz) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function &foo($a) {}',
                '<?php function &foo( $a ) {}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function foo($a)
	{}',
                '<?php function foo( $a)
	{}',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php
    function foo(
        $a,
        $b,
        $c
    ) {}',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php $function = function() {};',
                '<?php $function = function (){};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php $function("");',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function($a) use ($b) {};',
                '<?php function ($a)use($b) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function($a) use ($b) {};',
                '<?php function ($a)         use      ($b) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function($a) use ($b) {};',
                '<?php function ($a) use ( $b ) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function&($a) use ($b) {};',
                '<?php function &(  $a   ) use (   $b      ) {};',
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
                null,
                self::$configurationClosureSpacingNone,
            ],
            // do not remove multiline space before { when end of previous line is a comment
            [
                '<?php
function foo() // bar
{              // baz
}',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php
function foo() /* bar */
{              /* baz */
}',
                null,
                self::$configurationClosureSpacingNone,
            ],
            [
                '<?php function #
foo#
 (#
 ) #
{#
}#',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFix54Cases
     */
    public function test54($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix54Cases()
    {
        return [
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function test70($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            ['<?php use function Foo\bar; bar ( 1 );'],
            ['<?php use function some\test\{fn_a, fn_b, fn_c};'],
            ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>'],
            ['<?php use function Foo\bar; bar ( 1 );', null, self::$configurationClosureSpacingNone],
            ['<?php use function some\test\{fn_a, fn_b, fn_c};', null, self::$configurationClosureSpacingNone],
            ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>', null, self::$configurationClosureSpacingNone],
        ];
    }
}
