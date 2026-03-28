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
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoUnreachableDefaultArgumentValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php function bFunction($foo, $bar) {}',
            '<?php function bFunction($foo = null, $bar) {}',
        ];

        yield [
            '<?php function bFunction($foo, $bar) {}',
            '<?php function bFunction($foo = "two words", $bar) {}',
        ];

        yield [
            '<?php function cFunction($foo, $bar, $baz) {}',
            '<?php function cFunction($foo = false, $bar = "bar", $baz) {}',
        ];

        yield [
            '<?php function dFunction($foo, $bar, $baz) {}',
            '<?php function dFunction($foo = false, $bar, $baz) {}',
        ];

        yield [
            '<?php function foo (Foo $bar = null, $baz) {}',
        ];

        yield [
            '<?php function eFunction($foo, $bar, \SplFileInfo $baz, $x = "default") {}',
            '<?php function eFunction($foo, $bar = "removedDefault", \SplFileInfo $baz, $x = "default") {}',
        ];

        yield [
            <<<'EOT'
                                    <?php
                                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};

                                        function fFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};
                EOT,
            <<<'EOT'
                                    <?php
                                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};

                                        function fFunction($foo, $bar = 'removedValue', \SplFileInfo $baz, $x = 'default') {};
                EOT,
        ];

        yield [
            '<?php function foo ($bar /* a */  /* b */ , $c) {}',
            '<?php function foo ($bar /* a */ = /* b */ 1, $c) {}',
        ];

        yield [
            '<?php function hFunction($foo,$bar,\SplFileInfo $baz,$x = 5) {};',
            '<?php function hFunction($foo,$bar="removedValue",\SplFileInfo $baz,$x = 5) {};',
        ];

        yield [
            '<?php function eFunction($foo, $bar, \SplFileInfo $baz = null, $x) {}',
            '<?php function eFunction($foo = PHP_EOL, $bar, \SplFileInfo $baz = null, $x) {}',
        ];

        yield [
            '<?php function eFunction($foo, $bar) {}',
            '<?php function eFunction($foo       = null, $bar) {}',
        ];

        yield [
            <<<'EOT'
                                    <?php
                                        function foo(
                                            $a, // test
                                            $b, /* test */
                                            $c, // abc
                                            $d
                                        ) {}
                EOT,
            <<<'EOT'
                                    <?php
                                        function foo(
                                            $a = 1, // test
                                            $b = 2, /* test */
                                            $c = null, // abc
                                            $d
                                        ) {}
                EOT,
        ];

        yield [
            '<?php function foo($foo, $bar) {}',
            '<?php function foo($foo = array(array(1)), $bar) {}',
        ];

        yield [
            '<?php function a($a, $b) {}',
            '<?php function a($a = array("a" => "b", "c" => "d"), $b) {}',
        ];

        yield [
            '<?php function a($a, $b) {}',
            '<?php function a($a = ["a" => "b", "c" => "d"], $b) {}',
        ];

        yield [
            '<?php function a($a, $b) {}',
            '<?php function a($a = NULL, $b) {}',
        ];

        yield [
            '<?php function a(\SplFileInfo $a = Null, $b) {}',
        ];

        yield [
            '<?php function a(array $a = null, $b) {}',
        ];

        yield [
            '<?php function a(callable $a = null, $b) {}',
        ];

        yield [
            '<?php function a(\SplFileInfo &$a = Null, $b) {}',
        ];

        yield [
            '<?php function a(&$a, $b) {}',
            '<?php function a(&$a = null, $b) {}',
        ];

        yield [
            '<?php $fnc = function ($a, $b = 1) use ($c) {};',
        ];

        yield [
            '<?php $fnc = function ($a, $b) use ($c) {};',
            '<?php $fnc = function ($a = 1, $b) use ($c) {};',
        ];

        yield [
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
        ];

        yield [
            '<?php function a($a = 1, ...$b) {}',
        ];

        yield [
            '<?php function a($a = 1, \SplFileInfo ...$b) {}',
        ];

        yield [
            '<?php function foo (?Foo $bar, $baz) {}',
            '<?php function foo (?Foo $bar = null, $baz) {}',
        ];

        yield [
            '<?php function foo (?Foo $bar = null, ?Baz $baz = null) {}',
        ];

        yield [
            '<?php $fn = fn ($a, $b) => null;',
            '<?php $fn = fn ($a = 1, $b) => null;',
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
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'handle trailing comma' => [
            '<?php function foo($x, $y = 42, $z = 42 ) {}',
        ];

        yield 'handle attributes without arguments' => [
            '<?php function foo(
                #[Attribute1] $x,
                #[Attribute2] $y,
                #[Attribute3] $z
            ) {}',
            '<?php function foo(
                #[Attribute1] $x,
                #[Attribute2] $y = 42,
                #[Attribute3] $z
            ) {}',
        ];

        yield 'handle attributes with arguments' => [
            '<?php function foo(
                #[Attribute1(1, 2)] $x,
                #[Attribute2(3, 4)] $y,
                #[Attribute3(5, 6)] $z
            ) {}',
            '<?php function foo(
                #[Attribute1(1, 2)] $x,
                #[Attribute2(3, 4)] $y = 42,
                #[Attribute3(5, 6)] $z
            ) {}',
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
     * @return iterable<string, array{string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'do not crash' => [
            '<?php strlen( ... );',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP 8.4
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->testFix($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'property hooks' => [
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private int $a { set(int $x) { $this->a = $x; } },
                        private int $b { set(int $x) { $this->b = $x; } },
                        private int $c { set(int $x) { $this->c = $x; } },
                        private int $d { set(int $x) { $this->d = $x; } },
                        private int $e = 3 { set(int $x) { $this->e = $x; } },
                    ) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private int $a { set(int $x) { $this->a = $x; } },
                        private int $b = 1 { set(int $x) { $this->b = $x; } },
                        private int $c = 2 { set(int $x) { $this->c = $x; } },
                        private int $d { set(int $x) { $this->d = $x; } },
                        private int $e = 3 { set(int $x) { $this->e = $x; } },
                    ) {}
                }
                PHP,
        ];

        yield 'do not crash' => [<<<'PHP'
            <?php class Foo
            {
                public function __construct(
                    public string $myVar {
                        set(string $value) {
                            $this->myVar = $value;
                        }
                    },
                ) {}
            }
            PHP
        ];

        yield 'do not crash 2' => [<<<'PHP'
            <?php class Foo
            {
                public function __construct(
                    public string $key {
                        set(string $key) => $this->key = mb_strtolower($key);
                    },
                    public int $value,
                ) {}
            }
            PHP
        ];
    }

    /**
     * @dataProvider provideFix85Cases
     *
     * @requires PHP 8.5
     */
    public function testFix85(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix85Cases(): iterable
    {
        yield 'closure' => [
            '<?php function foo(Closure $x, int $y) {}',
            '<?php function foo(Closure $x = static function ($a, $b): void {}, int $y) {}',
        ];

        yield 'closure with default' => [
            '<?php function foo(int $x = 1, Closure $y = static function ($a, $b, $c): void {}, int $z = 3) {}',
            '<?php function foo(int $x = 1, Closure $y = static function ($a, $b = 2, $c): void {}, int $z = 3) {}',
        ];

        yield 'closures' => [
            <<<'PHP'
                <?php function foo(
                    Closure $a,
                    Closure $b,
                    Closure $c,
                    Closure $d,
                    Closure $e = static function ($a, $b, $c, $d = 'd'): void {},
                ) {}
                PHP,
            <<<'PHP'
                <?php function foo(
                    Closure $a,
                    Closure $b = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                    Closure $c = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                    Closure $d,
                    Closure $e = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                ) {}
                PHP,
        ];

        yield 'closures for promoted properties' => [
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private Closure $a,
                        private Closure $b,
                        private Closure $c,
                        private Closure $d,
                        private Closure $e = static function ($a, $b, $c, $d = 'd'): void {},
                    ) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private Closure $a,
                        private Closure $b = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                        private Closure $c = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                        private Closure $d,
                        private Closure $e = static function ($a, $b = 'b', $c, $d = 'd'): void {},
                    ) {}
                }
                PHP,
        ];

        yield 'closure and property hooks' => [
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private Closure $a { set(Closure $x) { $this->a = $x; } },
                        private Closure $b { set(Closure $x) { $this->b = $x; } },
                        private Closure $c { set(Closure $x) { $this->c = $x; } },
                        private Closure $d { set(Closure $x) { $this->d = $x; } },
                        private Closure $e = static function ($a, $b): void {} { set(Closure $x) { $this->e = $x; } },
                    ) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo
                {
                    public function __construct(
                        private Closure $a { set(Closure $x) { $this->a = $x; } },
                        private Closure $b = static function ($a = 1, $b): void {} { set(Closure $x) { $this->b = $x; } },
                        private Closure $c = static function ($a = 2, $b): void {} { set(Closure $x) { $this->c = $x; } },
                        private Closure $d { set(Closure $x) { $this->d = $x; } },
                        private Closure $e = static function ($a = 3, $b): void {} { set(Closure $x) { $this->e = $x; } },
                    ) {}
                }
                PHP,
        ];
    }
}
