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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer
 */
final class NoTrailingCommaInSinglelineFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php [$x, $y] = $a;',
            '<?php [$x, $y,] = $a;',
        ];

        yield 'group_import' => [
            '<?php use a\{ClassA, ClassB};',
            '<?php use a\{ClassA, ClassB,};',
        ];

        yield 'lots of nested' => [
            '<?php $a = [1,[1,[1,[1,[1,[1,[1,[1]],[1,[1,[1,[1,[1,[1,[1,[1]]]]]]]]]]]]]];',
            '<?php $a = [1,[1,[1,[1,[1,[1,[1,[1,],],[1,[1,[1,[1,[1,[1,[1,[1,],],],],],],],],],],],],],];',
        ];
    }

    /**
     * @dataProvider provideFixNoTrailingCommaInSinglelineFunctionCallCases
     */
    public function testFixNoTrailingCommaInSinglelineFunctionCall(string $expected, string $input = null): void
    {
        $this->fixer->configure(['elements' => ['arguments']]);

        $this->doTest($expected, $input);
    }

    public static function provideFixNoTrailingCommaInSinglelineFunctionCallCases(): iterable
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
            '<?php (foo($a/* 1X */   /* 2 */  ));',
            '<?php (foo($a /* 1X */  , /* 2 */  ));',
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
',
            '<?php
unset($foo->bar,);
$b = isset($foo->bar,);
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
     * @dataProvider provideFix80NoTrailingCommaInSinglelineFunctionCallFixerCases
     *
     * @requires PHP 8.0
     */
    public function testFix80NoTrailingCommaInSinglelineFunctionCallFixer(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80NoTrailingCommaInSinglelineFunctionCallFixerCases(): iterable
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
     * @dataProvider provideFix81NoTrailingCommaInSinglelineFunctionCallFixerCases
     *
     * @requires PHP 8.1
     */
    public function testFix81NoTrailingCommaInSinglelineFunctionCallFixer(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81NoTrailingCommaInSinglelineFunctionCallFixerCases(): iterable
    {
        yield [
            '<?php $object?->method(1); strlen(...);',
            '<?php $object?->method(1,); strlen(...);',
        ];
    }

    /**
     * @dataProvider provideFixNoTrailingCommaInSinglelineArrayFixerCases
     */
    public function testFixNoTrailingCommaInSinglelineArrayFixer(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['elements' => ['array']]);

        $this->doTest($expected, $input);
    }

    public static function provideFixNoTrailingCommaInSinglelineArrayFixerCases(): array
    {
        return [
            ['<?php $x = array();'],
            ['<?php $x = array("foo");'],
            [
                '<?php $x = array("foo");',
                '<?php $x = array("foo", );',
            ],
            ["<?php \$x = array(\n'foo', \n);"],
            ["<?php \$x = array('foo', \n);"],
            ["<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"],
            ["<?php \$x = array(array('foo',\n), \n);"],
            [
                '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array(
        "foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array(
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ],
            // Short syntax
            ['<?php $x = array([]);'],
            ['<?php $x = [[]];'],
            ['<?php $x = ["foo"];', '<?php $x = ["foo",];'],
            ['<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'],
            ["<?php \$x = bar([['foo'],\n]);"],
            ["<?php \$x = ['foo', \n];"],
            ['<?php $x = array([]);', '<?php $x = array([],);'],
            ['<?php $x = [[]];', '<?php $x = [[],];'],
            ['<?php $x = [$y[""]];', '<?php $x = [$y[""],];'],
            [
                '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = [
        "foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = [
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php $x = array(...$foo);',
                '<?php $x = array(...$foo, );',
            ],
            [
                '<?php $x = [...$foo];',
                '<?php $x = [...$foo, ];',
            ],
        ];
    }

    /**
     * @dataProvider provideFixNoTrailingCommaInListCallFixerCases
     */
    public function testFixNoTrailingCommaInListCallFixer(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['elements' => ['array_destructuring']]);

        $this->doTest($expected, $input);
    }

    public static function provideFixNoTrailingCommaInListCallFixerCases(): iterable
    {
        yield [
            '<?php
list($a1, $b) = foo();
list($a2, , $c, $d) = foo();
list($a3, , $c) = foo();
list($a4) = foo();
list($a5 , $b) = foo();
list($a6, /* $b */, $c) = foo();
',
            '<?php
list($a1, $b) = foo();
list($a2, , $c, $d, ) = foo();
list($a3, , $c, , ) = foo();
list($a4, , , , , ) = foo();
list($a5 , $b , ) = foo();
list($a6, /* $b */, $c, ) = foo();
',
        ];

        yield [
            '<?php
list(
$a#
,#
#
) = $a;',
        ];

        yield [
            '<?php
[$a7, $b] = foo();
[$a8, , $c, $d] = foo();
[$a9, , $c] = foo();
[$a10] = foo();
[$a11 , $b] = foo();
[$a12, /* $b */, $c] = foo();
',
            '<?php
[$a7, $b] = foo();
[$a8, , $c, $d, ] = foo();
[$a9, , $c, , ] = foo();
[$a10, , , , , ] = foo();
[$a11 , $b , ] = foo();
[$a12, /* $b */, $c, ] = foo();
',
        ];
    }
}
