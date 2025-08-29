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
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoTrailingCommaInSinglelineFunctionCallFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\NoTrailingCommaInSinglelineFunctionCallFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoTrailingCommaInSinglelineFunctionCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple var' => [
            '<?php $a(1);',
            '<?php $a(1,);',
        ];

        yield '&' => [
            '<?php $a = &foo($a);',
            '<?php $a = &foo($a,);',
        ];

        yield 'open' => [
            '<?php foo($a);',
            '<?php foo($a,);',
        ];

        yield '=' => [
            '<?php $b = foo($a);',
            '<?php $b = foo($a,);',
        ];

        yield '.' => [
            '<?php $c = $b . foo($a);',
            '<?php $c = $b . foo($a,);',
        ];

        yield '(' => [
            '<?php (foo($a/* 1 */   /* 2 */  ));',
            '<?php (foo($a /* 1 */  , /* 2 */  ));',
        ];

        yield '\\' => [
            '<?php \foo($a);',
            '<?php \foo($a,);',
        ];

        yield 'A\\' => [
            '<?php A\foo($a);',
            '<?php A\foo($a,);',
        ];

        yield '\A\\' => [
            '<?php \A\foo($a);',
            '<?php \A\foo($a,);',
        ];

        yield ';' => [
            '<?php ; foo($a);',
            '<?php ; foo($a,);',
        ];

        yield '}' => [
            '<?php if ($a) { echo 1;} foo($a);',
            '<?php if ($a) { echo 1;} foo($a,);',
        ];

        yield 'test method call' => [
            '<?php $o->abc($a);',
            '<?php $o->abc($a,);',
        ];

        yield 'nested call' => [
            '<?php $o->abc($a,foo(1));',
            '<?php $o->abc($a,foo(1,));',
        ];

        yield 'wrapped' => [
            '<?php echo (new Process())->getOutput(1);',
            '<?php echo (new Process())->getOutput(1,);',
        ];

        yield 'dynamic function and method calls' => [
            '<?php $b->$a(1); $c("");',
            '<?php $b->$a(1,); $c("",);',
        ];

        yield 'static function call' => [
            '<?php
unset($foo->bar);
$b = isset($foo->bar);
list($a,$b) = $a;
',
            '<?php
unset($foo->bar,);
$b = isset($foo->bar,);
list($a,$b,) = $a;
',
        ];

        yield 'unset' => [
            '<?php A::foo(1);',
            '<?php A::foo(1,);',
        ];

        yield 'anonymous_class construction' => [
            '<?php new class(1, 2) {};',
            '<?php new class(1, 2,) {};',
        ];

        yield 'array/property access call' => [
            '<?php
$a = [
    "e" => static function(int $a): void{ echo $a;},
    "d" => [
        [2 => static function(int $a): void{ echo $a;}]
    ]
];

$a["e"](1);
$a["d"][0][2](1);

$z = new class { public static function b(int $a): void {echo $a; }};
$z::b(1);

${$e}(1);
$$e(2);
$f(0)(1);
$g["e"](1); // foo',
            '<?php
$a = [
    "e" => static function(int $a): void{ echo $a;},
    "d" => [
        [2 => static function(int $a): void{ echo $a;}]
    ]
];

$a["e"](1,);
$a["d"][0][2](1,);

$z = new class { public static function b(int $a): void {echo $a; }};
$z::b(1,);

${$e}(1,);
$$e(2,);
$f(0,)(1,);
$g["e"](1,); // foo',
        ];

        yield 'do not fix' => [
            '<?php
                function someFunction ($p1){}
                function & foo($a,$b): array { return []; }

                foo (
                    1,
                    2,
                );

                $a = new class (
                    $a,
                ) {};

                isset($a, $b);
                unset($a,$b);
                list($a,$b) = $a;

                $a = [1,2,3,];
                $a = array(1,2,3,);

                function foo1(string $param = null ): void
                {
                }
            ;',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(
    #[MyAttr(1, 2,)] Type $myParam,
) {}

$foo1b = function() use ($bar, ) {};
',
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
            '<?php $object?->method(1); strlen(...);',
            '<?php $object?->method(1,); strlen(...);',
        ];
    }
}
