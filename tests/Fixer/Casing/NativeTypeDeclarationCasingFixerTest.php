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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeTypeDeclarationCasingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Casing\NativeTypeDeclarationCasingFixer>
 */
final class NativeTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP <8.0
     */
    public function testFixPre80(): void
    {
        $this->doTest('<?php
                class D {
                    private MIXED $m;
                };
            ');
    }

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
        yield [
            '<?php
                function A(int $a): void {}

                class Foo
                {
                    private bool $c = false;
                    private bool $d = false;

                    public function B(int $a): bool { return $this->c || $this->d; }
                }

                function C(float $a): array { return [$a];}
                function D(array $a): array { return [$a];}
            ',
            '<?php
                function A(INT $a): VOID {}

                class Foo
                {
                    private BOOL $c = false;
                    private BOOL $d = false;

                    public function B(INT $a): BOOL { return $this->c || $this->d; }
                }

                function C(FLOAT $a): ARRAY { return [$a];}
                function D(ARRAY $a): ARRAY { return [$a];}
            ',
        ];

        yield [
            '<?php
class Foo
{
    private function Bar(array $bar) {
        return false;
    }
}
',
            '<?php
class Foo
{
    private function Bar(ARRAY $bar) {
        return false;
    }
}
',
        ];

        yield [
            '<?php
interface Foo
{
    public function Bar(array $bar);
}
',
            '<?php
interface Foo
{
    public function Bar(ArrAY $bar);
}
',
        ];

        yield [
            '<?php
function Foo(/**/array/**/$bar) {
    return false;
}
',
            '<?php
function Foo(/**/ARRAY/**/$bar) {
    return false;
}
',
        ];

        yield [
            '<?php
class Bar { function Foo(array $a, callable $b, self $c) {} }
                ',
            '<?php
class Bar { function Foo(ARRAY $a, CALLABLE $b, Self $c) {} }
                ',
        ];

        yield [
            '<?php
function Foo(INTEGER $a) {}
                ',
        ];

        yield [
            '<?php function Foo(
                    String\A $x,
                    B\String\C $y
                ) {}',
        ];

        yield [
            '<?php final class Foo1 { final public function Foo(bool $A, float $B, int $C, string $D): int {} }',
            '<?php final class Foo1 { final public function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {} }',
        ];

        yield [
            '<?php function Foo(bool $A, float $B, int $C, string $D): int {}',
            '<?php function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {}',
        ];

        yield [
            '<?php function Foo(): Foo\A { return new Foo(); }',
        ];

        yield [
            '<?php trait XYZ { function Foo(iterable $A): void {} }',
            '<?php trait XYZ { function Foo(ITERABLE $A): VOID {} }',
        ];

        yield [
            '<?php function Foo(iterable $A): void {}',
            '<?php function Foo(ITERABLE $A): VOID {}',
        ];

        yield [
            '<?php function Foo(?int $A): void {}',
            '<?php function Foo(?INT $A): VOID {}',
        ];

        yield [
            '<?php function Foo(string $A): ?/* */int {}',
            '<?php function Foo(STRING $A): ?/* */INT {}',
        ];

        yield [
            '<?php function Foo(object $A): void {}',
            '<?php function Foo(OBJECT $A): VOID {}',
        ];

        yield [
            '<?php return function (callable $c) {};',
            '<?php return function (CALLABLE $c) {};',
        ];

        yield [
            '<?php return fn (callable $c): int => 1;',
            '<?php return fn (CALLABLE $c): INT => 1;',
        ];

        yield [
            '<?php
                class Foo
                {
                    const A = 1;
                    const B = [];
                    const INT = "A"; // class constant; INT is the name of the const, not the type
                    const FLOAT=1.2;
                }

                const INT = "A"; // outside class; INT is the name of the const, not the type
            ',
        ];

        yield 'class properties single type' => [
            '<?php
                class D{}

                $a = new class extends D {
                    private array $ax;
                    private bool $bx = false;
                    private float $cx = 3.14;
                    private int $dx = 667;
                    private iterable $ex = [];
                    private object $f;
                    private parent $g;
                    private self $h;
                    private static $i;
                    private ?string $j;

                    private $INT = 1;
                    private FOO $bar;
                    private A\INT\B $z;
                };
            ',
            '<?php
                class D{}

                $a = new class extends D {
                    private ARRAY $ax;
                    private BOOL $bx = false;
                    private FLOAT $cx = 3.14;
                    private INT $dx = 667;
                    private ITERABLE $ex = [];
                    private OBJECT $f;
                    private PARENT $g;
                    private Self $h;
                    private STatic $i;
                    private ?STRIng $j;

                    private $INT = 1;
                    private FOO $bar;
                    private A\INT\B $z;
                };
            ',
        ];

        yield 'var keyword' => [
            '<?php class Foo {
                var $bar;
            }',
        ];

        yield 'static property without type' => [
            '<?php class Foo { static $bar; }',
            '<?php class Foo { STATIC $bar; }',
        ];

        yield 'dynamic property' => [
            '<?php class Foo {
                public function doFoo() {
                    $this->Object->doBar();
                }
            }',
        ];

        yield 'constants' => [
            <<<'PHP'
                <?php
                function f() {}
                if (True === $x) {
                } elseif (True == $y) {
                } elseif ($z === False) {}
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'class properties single type' => [
            '<?php
                class D {
                    private mixed $m;
                };
            ',
            '<?php
                class D {
                    private MIXED $m;
                };
            ',
        ];

        yield [
            '<?php class T { public function Foo(object $A): static {}}',
            '<?php class T { public function Foo(object $A): StatiC {}}',
        ];

        yield [
            '<?php class T { public function Foo(object $A): ?static {}}',
            '<?php class T { public function Foo(object $A): ?StatiC {}}',
        ];

        yield [
            '<?php class T { public function Foo(mixed $A): mixed {}}',
            '<?php class T { public function Foo(Mixed $A): MIXED {}}',
        ];

        yield 'mixed in arrow function' => [
            '<?php return fn (mixed $c): mixed => 1;',
            '<?php return fn (MiXeD $c): MIXED => 1;',
        ];

        yield [
            '<?php function foo(int|bool $x) {}',
            '<?php function foo(INT|BOOL $x) {}',
        ];

        yield [
            '<?php function foo(int | bool $x) {}',
            '<?php function foo(INT | BOOL $x) {}',
        ];

        yield [
            '<?php function foo(): int|bool {}',
            '<?php function foo(): INT|BOOL {}',
        ];

        yield 'return type string|false' => [
            '<?php function foo(): string|false {}',
            '<?php function foo(): string|FALSE {}',
        ];

        yield 'return type string|null' => [
            '<?php function foo(): string|null {}',
            '<?php function foo(): string|NULL {}',
        ];

        yield 'union types in arrow function' => [
            '<?php return fn (string|null $c): int|null => 1;',
            '<?php return fn (string|NULL $c): INT|NULL => 1;',
        ];

        yield 'union Types' => [
            '<?php $a = new class {
                    private null|int|bool $a4 = false;
                };',
            '<?php $a = new class {
                    private NULL|INT|BOOL $a4 = false;
                };',
        ];

        yield 'promoted properties' => [
            <<<'PHP'
                <?php class Foo extends Bar {
                    public function __construct(
                        public int $i,
                        protected parent $p,
                        private string $s
                    ) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo extends Bar {
                    public function __construct(
                        public INT $i,
                        protected PARENT $p,
                        private STRING $s
                    ) {}
                }
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'return type `never`' => [
            '<?php class T { public function Foo(object $A): never {die;}}',
            '<?php class T { public function Foo(object $A): NEVER {die;}}',
        ];

        yield 'class readonly property' => [
            '<?php class Z {
                    private readonly array $ax;
                };',
            '<?php class Z {
                    private readonly ARRAY $ax;
                };',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'disjunctive normal form types in arrow function' => [
            '<?php return fn ((A&B)|C|null $c): (X&Y)|Z|null => 1;',
            '<?php return fn ((A&B)|C|Null $c): (X&Y)|Z|NULL => 1;',
        ];

        yield 'iterable: disjunctive normal form types in arrow function' => [
            '<?php return fn (iterable|C|null $c): (X&Y)|Z|null => 1;',
            '<?php return fn (ITERABLE|C|Null $c): (X&Y)|Z|NULL => 1;',
        ];

        foreach (['true', 'false', 'null'] as $type) {
            yield \sprintf('standalone type `%s` in class method', $type) => [
                \sprintf('<?php class T { public function Foo(%s $A): %1$s {return $A;}}', $type),
                \sprintf('<?php class T { public function Foo(%s $A): %1$s {return $A;}}', strtoupper($type)),
            ];

            yield \sprintf('standalone type `%s` in function', $type) => [
                \sprintf('<?php function Foo(%s $A): %1$s {return $A;}', $type),
                \sprintf('<?php function Foo(%s $A): %1$s {return $A;}', strtoupper($type)),
            ];

            yield \sprintf('standalone type `%s` in closure', $type) => [
                \sprintf('<?php array_filter([], function (%s $A): %1$s {return $A;});', $type),
                \sprintf('<?php array_filter([], function (%s $A): %1$s {return $A;});', strtoupper($type)),
            ];

            yield \sprintf('standalone type `%s` in arrow function', $type) => [
                \sprintf('<?php array_filter([], fn (%s $A): %1$s => $A);', $type),
                \sprintf('<?php array_filter([], fn (%s $A): %1$s => $A);', strtoupper($type)),
            ];
        }

        yield 'intersection Types' => [
            '<?php $a = new class {
                    private (A&B)|int|D $d5;
                    private (A\STRING\B&B\INT\C)|int|(A&B) $e6;
                };',
            '<?php $a = new class {
                    private (A&B)|INT|D $d5;
                    private (A\STRING\B&B\INT\C)|int|(A&B) $e6;
                };',
        ];
    }

    /**
     * @dataProvider provideFix83Cases
     *
     * @requires PHP 8.3
     */
    public function testFix83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield 'simple case' => [
            '<?php
                class Foo
                {
                    const int FOO = 6;
                }
            ',
            '<?php
                class Foo
                {
                    const INT FOO = 6;
                }
            ',
        ];

        yield 'single types' => [
            '<?php
                class Foo
                {
                    const int SOME_INT = 3;
                    const array SOME_ARRAY = [7];
                    const float SOME_FLOAT = 1.23;
                    const iterable SOME_ITERABLE = [1, 2];
                    const mixed SOME_MIXED = 1;
                    const null SOME_NULL = NULL;
                    const string SOME_STRING = "X";
                }
            ',
            '<?php
                class Foo
                {
                    const INT SOME_INT = 3;
                    const ARRAY SOME_ARRAY = [7];
                    const Float SOME_FLOAT = 1.23;
                    const ITERABLE SOME_ITERABLE = [1, 2];
                    const MIXED SOME_MIXED = 1;
                    const NULL SOME_NULL = NULL;
                    const STRING SOME_STRING = "X";
                }
            ',
        ];

        yield 'nullables `self`, `parent` and `object`' => [
            '<?php
                class Foo extends FooParent
                {
                    const ?object SOME_OBJECT = NULL;
                    const ?parent SOME_PARENT = NULL;
                    const ?self SOME_SELF = NULL;
                    const ?int/* x */A/* y */= 3;

                    const ?BAR B = null;
                    const ?BAR C = null;
                    const ?\BAR D = null;
                    const ?\INT\B E = null;
                    public const ?INT\A/* x */X=C;
                }
            ',
            '<?php
                class Foo extends FooParent
                {
                    const ?OBJECT SOME_OBJECT = NULL;
                    const ?PARENT SOME_PARENT = NULL;
                    const ?Self SOME_SELF = NULL;
                    const ?INT/* x */A/* y */= 3;

                    const ?BAR B = null;
                    const ?BAR C = null;
                    const ?\BAR D = null;
                    const ?\INT\B E = null;
                    public const ?INT\A/* x */X=C;
                }
            ',
        ];

        yield 'simple `|`' => [
            '<?php class Foo1 {
                const D|float BAR = 1.0;
            }',
            '<?php class Foo1 {
                const D|Float BAR = 1.0;
            }',
        ];

        yield 'multiple `|`' => [
            '<?php class Foo2 {
                const int|array|null|A\B|\C\D|float BAR_1 = NULL;
            }',
            '<?php class Foo2 {
                const INT|ARRAY|NULL|A\B|\C\D|FLOAT BAR_1 = NULL;
            }',
        ];

        yield 'handle mix of `|` and `(X&Y)`' => [
            '<?php
                class Foo extends FooParent
                {
                    private const Z|A\S\T\R|int|float|iterable SOMETHING = 123;
                    protected const \A\B|(Foo & Stringable )|null|\X D = null;

                    public const Foo&Stringable G = V::A;
                }
            ',
            '<?php
                class Foo extends FooParent
                {
                    private const Z|A\S\T\R|INT|FLOAT|ITERABLE SOMETHING = 123;
                    protected const \A\B|(Foo & Stringable )|NULL|\X D = null;

                    public const Foo&Stringable G = V::A;
                }
            ',
        ];

        yield 'interface, nullable int' => [
            '<?php interface SomeInterface {
                const ?int FOO = 1;
            }',
            '<?php interface SomeInterface {
                const ?INT FOO = 1;
            }',
        ];

        yield 'trait, string' => [
            '<?php trait T {
                const string TEST = E::TEST;
            }',
            '<?php trait T {
                const STRING TEST = E::TEST;
            }',
        ];

        yield 'enum, int' => [
            '<?php enum E: string {
                case Hearts = "H";

                const int TEST = 789;
                const self A = self::Hearts;
                const static B = self::Hearts;
            }',
            '<?php enum E: STRING {
                case Hearts = "H";

                const INT TEST = 789;
                const SELF A = self::Hearts;
                const STATIC B = self::Hearts;
            }',
        ];

        yield 'enum with "Mixed" case' => [
            <<<'PHP'
                <?php
                enum Foo
                {
                    case Mixed;
                    public function bar()
                    {
                        self::Mixed;
                    }
                }
                PHP,
        ];

        yield 'do not fix' => [
            '<?php class Foo {
                PUBLIC CONST FOO&STRINGABLE G = A::B;
            }

            CONST A = 1;',
        ];

        yield 'fix "false" in type' => [
            '<?php class Foo { private false|int $bar; private false $baz; }',
            '<?php class Foo { private FALSE|INT $bar; private FALSE $baz; }',
        ];
    }
}
