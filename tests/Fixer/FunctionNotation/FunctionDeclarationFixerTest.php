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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
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
    /**
     * @var array<string,string>
     */
    private static $configurationClosureSpacingNone = ['closure_function_spacing' => FunctionDeclarationFixer::SPACING_NONE];

    /**
     * @var array<string,string>
     */
    private static $configurationArrowSpacingNone = ['closure_fn_spacing' => FunctionDeclarationFixer::SPACING_NONE];

    public function testInvalidConfigurationClosureFunctionSpacing(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^\[function_declaration\] Invalid configuration: The option "closure_function_spacing" with value "neither" is invalid\. Accepted values are: "none", "one"\.$#'
        );

        $this->fixer->configure(['closure_function_spacing' => 'neither']);
    }

    public function testInvalidConfigurationClosureFnSpacing(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^\[function_declaration\] Invalid configuration: The option "closure_fn_spacing" with value "neither" is invalid\. Accepted values are: "none", "one"\.$#'
        );

        $this->fixer->configure(['closure_fn_spacing' => 'neither']);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
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
            ['<?php use function Foo\bar; bar ( 1 );'],
            ['<?php use function some\test\{fn_a, fn_b, fn_c};'],
            ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>'],
            ['<?php use function Foo\bar; bar ( 1 );', null, self::$configurationClosureSpacingNone],
            ['<?php use function some\test\{fn_a, fn_b, fn_c};', null, self::$configurationClosureSpacingNone],
            ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>', null, self::$configurationClosureSpacingNone],
            [
                '<?php fn ($i) => null;',
                '<?php fn($i) => null;',
            ],
            [
                '<?php fn ($a) => null;',
                '<?php fn ($a)     => null;',
            ],
            [
                '<?php $fn = fn () => null;',
                '<?php $fn = fn()=> null;',
            ],
            [
                '<?php fn &($a) => null;',
                '<?php fn &(  $a   ) => null;',
            ],
            [
                '<?php fn($i) => null;',
                null,
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php fn($a) => null;',
                '<?php fn ($a)      => null;',
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php $fn = fn() => null;',
                '<?php $fn = fn ()=> null;',
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php $fn("");',
                null,
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php fn&($a) => null;',
                '<?php fn &(  $a   ) => null;',
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php fn&($a,$b) => null;',
                '<?php fn &(  $a,$b  ) => null;',
                self::$configurationArrowSpacingNone,
            ],
            [
                '<?php $b = static fn ($a) => $a;',
                '<?php $b = static     fn( $a )   => $a;',
            ],
            [
                '<?php $b = static fn($a) => $a;',
                '<?php $b = static     fn ( $a )   => $a;',
                self::$configurationArrowSpacingNone,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideFixPhp80Cases(): iterable
    {
        yield [
            '<?php function ($i) {};',
            '<?php function(   $i,   ) {};',
        ];

        yield [
            '<?php function (
                $a,
                $b,
                $c,
            ) {};',
            '<?php function(
                $a,
                $b,
                $c,
            ) {};',
        ];

        yield [
            '<?php function foo(
                $a,
                $b,
                $c,
            ) {}',
            '<?php function foo    (
                $a,
                $b,
                $c,
            ){}',
        ];

        yield [
            '<?php
                    $b = static function ($a,$b) {
                        echo $a;
                    };
                ',
            '<?php
                    $b = static     function(  $a,$b,   )   {
                        echo $a;
                    };
                ',
        ];

        yield [
            '<?php fn&($a,$b) => null;',
            '<?php fn &(  $a,$b,   ) => null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php
                function ($a) use ($b) {};
                function ($y) use (
                    $b,
                    $c,
                ) {};
            ',
            '<?php
                function ($a) use ($b  ,  )     {};
                function ($y) use (
                    $b,
                    $c,
                ) {};
            ',
        ];

        yield [
            '<?php function ($i,) {};',
            '<?php function(   $i,   ) {};',
            ['trailing_comma_single_line' => true],
        ];
    }
}
