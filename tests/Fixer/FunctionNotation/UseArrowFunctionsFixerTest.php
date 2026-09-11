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

use PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer>
 *
 * @author Gregor Harlan
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(UseArrowFunctionsFixer::class)]
final class UseArrowFunctionsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php foo(function () use ($a, &$b) { return 1; });',
        ];

        yield [
            '<?php foo(function () { bar(); return 1; });',
        ];

        yield [
            '<?php foo(fn()=> 1);',
            '<?php foo(function(){return 1;});',
        ];

        yield [
            '<?php foo(fn()=>$a);',
            '<?php foo(function()use($a){return$a;});',
        ];

        yield [
            '<?php foo( fn () => 1 );',
            '<?php foo( function () { return 1; } );',
        ];

        yield [
            '<?php $func = static fn &(array &$a, string ...$b): ?int => 1;',
            '<?php $func = static function &(array &$a, string ...$b): ?int { return 1; };',
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(1, fn (int $a, Foo $b) => bar($a, $c), 2);
                EXPECTED,
            <<<'INPUT'
                <?php
                    foo(1, function (int $a, Foo $b) use ($c, $d) {
                        return bar($a, $c);
                    }, 2);
                INPUT,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(fn () => 1);
                EXPECTED,
            <<<'INPUT'
                <?php
                    foo(function () {


                        return 1;


                    });
                INPUT,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(fn ($a) => fn () => $a + 1);
                EXPECTED,
            <<<'INPUT'
                <?php
                    foo(function ($a) {
                        return function () use ($a) {
                            return $a + 1;
                        };
                    });
                INPUT,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {// comment
                        return 1;
                    });
                EXPECTED,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        // comment
                        return 1;
                    });
                EXPECTED,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        return 1; // comment
                    });
                EXPECTED,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        return 1;
                        // comment
                    });
                EXPECTED,
        ];

        yield [
            <<<'PHP'
                <?php
                    foo(fn () =>
                            1);
                PHP,
            <<<'PHP'
                <?php
                    foo(function () {
                        return
                            1;
                    });
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                    $func = fn (
                        $a,
                        $b
                    ) => 1;
                PHP,
            <<<'PHP'
                <?php
                    $func = function (
                        $a,
                        $b
                    ) {
                        return 1;
                    };
                PHP,
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    $func = function () {
                        return function () {
                            foo();
                        };
                    };
                EXPECTED,
        ];

        yield [
            '<?php $testDummy = fn () => null;',
            '<?php $testDummy = function () { return; };',
        ];

        yield [
            '<?php $testDummy = fn () => null ;',
            '<?php $testDummy = function () { return ; };',
        ];

        yield [
            '<?php $testDummy = fn () => null/* foo */;',
            '<?php $testDummy = function () { return/* foo */; };',
        ];

        yield [
            <<<'PHP'
                <?php return fn () => [
                        CONST_A,
                        CONST_B,
                    ];
                PHP,
            <<<'PHP'
                <?php return function () {
                    return [
                        CONST_A,
                        CONST_B,
                    ];
                };
                PHP,
        ];

        yield [
            '<?php
            foo(
                fn () => 42
                        '.'
            );',
            '<?php
            foo(
                function () {
                    return 42
                        ;
                }
            );',
        ];

        yield 'do not convert when closure with use() includes external file' => [
            '<?php
$load = \Closure::bind(static function ($path, $env) use ($container, $loader, $resource, $type) {
    return include $path;
}, null, null);',
        ];

        yield 'do not convert when closure with use() includes_once external file' => [
            '<?php
$load = function ($path) use ($config) {
    return include_once $path;
};',
        ];

        yield 'do not convert when closure with use() requires external file' => [
            '<?php
$load = function ($path) use ($data) {
    return require $path;
};',
        ];

        yield 'do not convert when closure with use() requires_once external file' => [
            '<?php
$load = function ($path) use ($settings) {
    return require_once $path;
};',
        ];

        yield 'convert when closure without use() includes external file' => [
            '<?php
$load = fn ($path) => include $path;',
            '<?php
$load = function ($path) {
    return include $path;
};',
        ];

        yield 'convert when closure with use() does not include external file' => [
            '<?php
$load = fn ($path) => $data[$path];',
            '<?php
$load = function ($path) use ($data) {
    return $data[$path];
};',
        ];
    }

    /**
     * @dataProvider provideFix85Cases
     *
     * @requires PHP >= 8.5.0
     */
    #[DataProvider('provideFix85Cases')]
    #[RequiresPhp('>= 8.5.0')]
    public function testFix85(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix85Cases(): iterable
    {
        foreach ([
            'global constant' => '<?php const CALLBACK = static function () { return 1; };',
            'class constant' => '<?php class Holder { const CALLBACK = static function () { return 1; }; }',
            'property default' => '<?php class Holder { public $callback = static function () { return 1; }; }',
            'parameter default' => '<?php function take($callback = static function () { return 1; }) {}',
            'reference-returning declaration parameter' => '<?php function &take($callback = static function () { return 1; }) { return $callback; }',
            'abstract declaration parameter' => '<?php interface Factory { public function take($callback = static function () { return 1; }); }',
            'arrow function parameter default' => '<?php $take = fn ($callback = static function () { return 1; }) => $callback;',
            'array in constant' => '<?php const CALLBACKS = [static function () { return 1; }, static function () { return 2; }];',
            'new expression in parameter default' => '<?php function take($holder = new Holder(static function () { return 1; })) {}',
            'constant before close tag' => '<?php const CALLBACK = static function () { return 1; } ?>',
        ] as $context => $code) {
            yield 'preserve closure in '.$context => [$code];
        }

        yield 'convert runtime closure after a constant declaration' => [
            '<?php const CALLBACK = static function () { return 1; }; $runtime = static fn () => 2;',
            '<?php const CALLBACK = static function () { return 1; }; $runtime = static function () { return 2; };',
        ];

        yield 'convert enclosing runtime closure but preserve its default' => [
            '<?php $take = fn ($callback = static function () { return 1; }) => $callback;',
            '<?php $take = function ($callback = static function () { return 1; }) { return $callback; };',
        ];

        yield 'convert runtime closure in constant closure body' => [
            '<?php const CALLBACK = static function () { return fn () => 1; };',
            '<?php const CALLBACK = static function () { return function () { return 1; }; };',
        ];

        yield 'convert runtime closure in attribute closure body' => [
            '<?php #[Callback(static function () { return fn () => 1; })] class Holder {}',
            '<?php #[Callback(static function () { return function () { return 1; }; })] class Holder {}',
        ];

        yield 'anonymous class arguments are runtime, defaults are constant' => [
            '<?php $holder = new class(static fn () => 1) { public $callback = static function () { return 2; }; };',
            '<?php $holder = new class(static function () { return 1; }) { public $callback = static function () { return 2; }; };',
        ];

        yield 'property hook body is runtime' => [
            '<?php class Holder { public Closure $callback { get => static fn () => 1; } }',
            '<?php class Holder { public Closure $callback { get => static function () { return 1; }; } }',
        ];

        yield 'do not convert closure in attribute' => [
            <<<'PHP'
                <?php
                class Foo {
                    function f1() {
                        return fn (int $x): int => 100 - $x;
                    }

                    #[Bar(callback: static function () { return true; })]
                    #[Baz(callback: static function (int $i): int { return $i + 100; })]
                    function f2() {
                        return static fn (int $x, int $y): int => 2 * $x + 3 * $y;
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo {
                    function f1() {
                        return function (int $x): int { return 100 - $x; };
                    }

                    #[Bar(callback: static function () { return true; })]
                    #[Baz(callback: static function (int $i): int { return $i + 100; })]
                    function f2() {
                        return static function (int $x, int $y): int { return 2 * $x + 3 * $y; };
                    }
                }
                PHP,
        ];
    }
}
