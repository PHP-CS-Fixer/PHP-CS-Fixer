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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ConstTypeRequiredFixer
 */
final class ConstTypeRequiredFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP 8.3
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'single constant, class' => [
            '<?php class Foo {const int A = 1;}',
            '<?php class Foo {const A = 1;}',
        ];

        yield 'single constant, interface' => [
            '<?php interface Foo {const float A = 2.567;}',
            '<?php interface Foo {const A = 2.567;}',
        ];

        yield 'single constant, trait' => [
            '<?php trait Foo {const string A = "xyz";}',
            '<?php trait Foo {const A = "xyz";}',
        ];

        yield 'single constant, enum' => [
            '<?php enum Foo {public const array A = [];}',
            '<?php enum Foo {public const A = [];}',
        ];

        yield 'single constant, anonymous class' => [
            '<?php
                $a = new class {
                    private const int B = 123;

                    public function foo(): void
                    {
                        echo self::B;
                    }
                };

                $a->foo();',
            '<?php
                $a = new class {
                    private const B = 123;

                    public function foo(): void
                    {
                        echo self::B;
                    }
                };

                $a->foo();',
        ];

        yield 'single constant, long array' => [
            '<?php enum Foo {public const array A = array(1,2,3);}',
            '<?php enum Foo {public const A = array(1,2,3);}',
        ];

        yield 'single constant, empty array' => [
            '<?php interface Foo {public const array A=[];}',
            '<?php interface Foo {public const A=[];}',
        ];

        yield 'single constant, null' => [
            '<?php class Foo{public const null A = null;}',
            '<?php class Foo{public const A = null;}',
        ];

        yield '2 constants, boolean' => [
            '<?php class Foo{public const bool A = true; const bool B = false;}',
            '<?php class Foo{public const A = true; const B = false;}',
        ];

        yield 'integer constants' => [
            '<?php class Foo{
                const int I3 = 1_234_567;
                const int I4 = 0x1A;
                const int I5 = 0b11111111;
            }',
            '<?php class Foo{
                const I3 = 1_234_567;
                const I4 = 0x1A;
                const I5 = 0b11111111;
            }',
        ];

        yield 'string constants' => [
            "<?php class Foo{
                const string S0 = b'foo';
                const string S1 = \"foo\";
            }",
            "<?php class Foo{
                const S0 = b'foo';
                const S1 = \"foo\";
            }",
        ];

        yield 'float constants, comments, PHPDoc' => [
            "<?php class Foo{
                const float F2A = 32312443231244323124432312443231244323124432312443231244323124432312443231244; // > max int
                const float/** 3 */F6 = /** 1 */1_234.567/** 2 */;
                const float F7/* 1 */ =/* 2 */2.567/* 3 *//* 4 */;
            }",
            "<?php class Foo{
                const F2A = 32312443231244323124432312443231244323124432312443231244323124432312443231244; // > max int
                const/** 3 */F6 = /** 1 */1_234.567/** 2 */;
                const F7/* 1 */ =/* 2 */2.567/* 3 *//* 4 */;
            }",
        ];

        yield 'composite constant values are mapped to `mixed`' => [
            "<?php
                class Foo
                {
                    const mixed C0 = 1 + 2;
                    const mixed C1 = (1 + 2) + 3;
                    const mixed C2 = (1 . 'a');
                }
            ",
            "<?php
                class Foo
                {
                    const C0 = 1 + 2;
                    const C1 = (1 + 2) + 3;
                    const C2 = (1 . 'a');
                }
            ",
        ];

        yield 'magic constants' => [
            '<?php
                class Foo1
                {
                    const string DIR = __DIR__;
                    const string FILE = __FILE__;
                    const string NAMESPACE = __NAMESPACE__;
                    const string CLASS_NAME = __CLASS__;
                    const string TRAIT = __TRAIT__;

                    const int LINE = __LINE__;
                }
            ',
            '<?php
                class Foo1
                {
                    const DIR = __DIR__;
                    const FILE = __FILE__;
                    const NAMESPACE = __NAMESPACE__;
                    const CLASS_NAME = __CLASS__;
                    const TRAIT = __TRAIT__;

                    const LINE = __LINE__;
                }
            ',
        ];

        yield 'combined; multiple candidates, nested anonymous classes and constants out of classy scope' => [
            '<?php
                const DO_NOT_FIX = 1;

                class Z {
                    const string B = "A";

                    public function test(): void
                    {
                        $a = new class {
                            private const int B = 123;

                            public function foo(): void
                            {
                                $b = new class {
                                    private const int C = 897;

                                    public function foo(): void
                                    {
                                        echo self::C;
                                    }
                                };

                                $b->foo();

                                echo self::B;
                            }
                        };

                        $a->foo();
                        echo static::B;
                    }
                }

                (new Z())->test();

                const DO_NOT_FIX_2 = 1;

                interface FooBar
                {
                    const float Y = 66.77;
                }
            ',
            '<?php
                const DO_NOT_FIX = 1;

                class Z {
                    const B = "A";

                    public function test(): void
                    {
                        $a = new class {
                            private const B = 123;

                            public function foo(): void
                            {
                                $b = new class {
                                    private const C = 897;

                                    public function foo(): void
                                    {
                                        echo self::C;
                                    }
                                };

                                $b->foo();

                                echo self::B;
                            }
                        };

                        $a->foo();
                        echo static::B;
                    }
                }

                (new Z())->test();

                const DO_NOT_FIX_2 = 1;

                interface FooBar
                {
                    const Y = 66.77;
                }
            ',
        ];

        yield 'predefined constants' => [
            '<?php class Foo {
                const float A = PHP_FLOAT_EPSILON;
                const int B = PHP_INT_MAX;
                const string C = PHP_EOL;
            }',
            '<?php class Foo {
                const A = PHP_FLOAT_EPSILON;
                const B = PHP_INT_MAX;
                const C = PHP_EOL;
            }',
        ];

        yield 'predefined constants with global namespace separator prefixed' => [
            '<?php class Foo {
                const float A = \PHP_FLOAT_MAX;
                const int B = \PHP_DEBUG;
                const string C = \PHP_OS;
            }',
            '<?php class Foo {
                const A = \PHP_FLOAT_MAX;
                const B = \PHP_DEBUG;
                const C = \PHP_OS;
            }',
        ];

        yield 'predefined/magic fully qualified class name' => [
            '<?php
class Foo
{
    public const string A = Foo::class;
    public const string B = \Foo::class;
    public const string C = \FooA\B\C::class;
    public const string D = self::class;
}',
            '<?php
class Foo
{
    public const A = Foo::class;
    public const B = \Foo::class;
    public const C = \FooA\B\C::class;
    public const D = self::class;
}',
        ];

        yield 'cannot evaluate value to exact type' => [
            '<?php
                class Foo
                {
                    const mixed E0 = E::A;
                    const mixed E1 = \A\B::Z;
                    const mixed E2 = [1, 2] + X::A;
                    const mixed E3 = array(1, 2) . X::A;
                    const mixed E4 = \GLOBAL_CONST;
                    const mixed E5 = GLOBAL_CONST_2;
                    const mixed E6 = \FooA\B\C::X;
                    const mixed E7 = self::U;
                    const mixed E8 = Foo::class . "A";
                    const mixed E9 = \Foo::class + C::D;
                    const mixed E10 = self::class + 4;
                    const mixed E11 = 4 + A::Z;
                }
            ',
            '<?php
                class Foo
                {
                    const E0 = E::A;
                    const E1 = \A\B::Z;
                    const E2 = [1, 2] + X::A;
                    const E3 = array(1, 2) . X::A;
                    const E4 = \GLOBAL_CONST;
                    const E5 = GLOBAL_CONST_2;
                    const E6 = \FooA\B\C::X;
                    const E7 = self::U;
                    const E8 = Foo::class . "A";
                    const E9 = \Foo::class + C::D;
                    const E10 = self::class + 4;
                    const E11 = 4 + A::Z;
                }
            ',
        ];

        yield 'already has type' => [
            '<?php
                class Foo
                {
                    public const int|null T0 = null;
                    protected const string|array T1 = "x";
                    private const iterable T2 = [1, 7, 9];
                }
            '
        ];

        yield 'constant outside of classy scope' => [
            '<?php
                class Foo {}
                const Z = 123;
            '
        ];
    }
}
