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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, null|string, 2?: array{elements?: array<string>}}>
     */
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

        yield 'simple var' => [
            '<?php $a(1);',
            '<?php $a(1,);',
            ['elements' => ['arguments']],
        ];

        yield '&' => [
            '<?php $a = &foo($a);',
            '<?php $a = &foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield 'open' => [
            '<?php foo($a);',
            '<?php foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield '=' => [
            '<?php $b = foo($a);',
            '<?php $b = foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield '.' => [
            '<?php $c = $b . foo($a);',
            '<?php $c = $b . foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield '(' => [
            '<?php (foo($a/* 1X */   /* 2 */  ));',
            '<?php (foo($a /* 1X */  , /* 2 */  ));',
            ['elements' => ['arguments']],
        ];

        yield '\\' => [
            '<?php \foo($a);',
            '<?php \foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield 'A\\' => [
            '<?php A\foo($a);',
            '<?php A\foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield '\A\\' => [
            '<?php \A\foo($a);',
            '<?php \A\foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield ';' => [
            '<?php ; foo($a);',
            '<?php ; foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield '}' => [
            '<?php if ($a) { echo 1;} foo($a);',
            '<?php if ($a) { echo 1;} foo($a,);',
            ['elements' => ['arguments']],
        ];

        yield 'test method call' => [
            '<?php $o->abc($a);',
            '<?php $o->abc($a,);',
            ['elements' => ['arguments']],
        ];

        yield 'nested call' => [
            '<?php $o->abc($a,foo(1));',
            '<?php $o->abc($a,foo(1,));',
            ['elements' => ['arguments']],
        ];

        yield 'wrapped' => [
            '<?php echo (new Process())->getOutput(1);',
            '<?php echo (new Process())->getOutput(1,);',
            ['elements' => ['arguments']],
        ];

        yield 'dynamic function and method calls' => [
            '<?php $b->$a(1); $c("");',
            '<?php $b->$a(1,); $c("",);',
            ['elements' => ['arguments']],
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
            ['elements' => ['arguments']],
        ];

        yield 'unset' => [
            '<?php A::foo(1);',
            '<?php A::foo(1,);',
            ['elements' => ['arguments']],
        ];

        yield 'anonymous_class construction' => [
            '<?php new class(1, 2) {};',
            '<?php new class(1, 2,) {};',
            ['elements' => ['arguments']],
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
            ['elements' => ['arguments']],
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
            null,
            ['elements' => ['arguments']],
        ];

        yield [
            '<?php $x = array();',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = array("foo");',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = array("foo");',
            '<?php $x = array("foo", );',
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = array(\n'foo', \n);",
            null,
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = array('foo', \n);",
            null,
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = array(array('foo'), \n);",
            "<?php \$x = array(array('foo',), \n);",
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = array(array('foo',\n), \n);",
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = array(
        "foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = array(
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            null,
            ['elements' => ['array']],
        ];

        // Short syntax
        yield [
            '<?php $x = array([]);',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = [[]];',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = ["foo"];',
            '<?php $x = ["foo",];',
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = bar(["foo"]);',
            '<?php $x = bar(["foo",]);',
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = bar([['foo'],\n]);",
            null,
            ['elements' => ['array']],
        ];

        yield [
            "<?php \$x = ['foo', \n];",
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = array([]);',
            '<?php $x = array([],);',
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = [[]];',
            '<?php $x = [[],];',
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = [$y[""]];',
            '<?php $x = [$y[""],];',
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = [
        "foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php
    $test = [
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            null,
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = array(...$foo);',
            '<?php $x = array(...$foo, );',
            ['elements' => ['array']],
        ];

        yield [
            '<?php $x = [...$foo];',
            '<?php $x = [...$foo, ];',
            ['elements' => ['array']],
        ];

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
            ['elements' => ['array_destructuring']],
        ];

        yield [
            '<?php
list(
$a#
,#
#
) = $a;',
            null,
            ['elements' => ['array_destructuring']],
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
            ['elements' => ['array_destructuring']],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

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

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php $object?->method(1); strlen(...);',
            '<?php $object?->method(1,); strlen(...);',
        ];
    }
}
