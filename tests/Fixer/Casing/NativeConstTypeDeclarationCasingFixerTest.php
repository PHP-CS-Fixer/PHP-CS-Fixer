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
 * @covers \PhpCsFixer\Fixer\Casing\NativeConstTypeDeclarationCasingFixer
 */
final class NativeConstTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    public function testDoNotFixCases(): void
    {
        $this->doTest(
            '<?php
                class Foo
                {
                    const A = 1;
                    const B = [];
                    const INT = "A"; // INT is the name of the const, not the type
                    const FLOAT=1.2;
                }

                const INT = "A"; // INT is the name of the const, not the type
                function foo_1(INT $a) {}
            ',
        );
    }

    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.3
     */
    public function testFix(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
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
            '<?php enum E {
                const int TEST = 789;
            }',
            '<?php enum E {
                const INT TEST = 789;
            }',
        ];

        yield 'do not fix' => [
            '<?php class Foo {
                PUBLIC FUNCTION FOO_1(INT|FLOAT|NULL|ITERABLE $A): VOID {}

                PUBLIC CONST FOO&STRINGABLE G = A::B;
            }

            enum E {
                PUBLIC CONST STATIC A = E::FOO;
                CASE FOO;
            }

            CONST A = 1;',
        ];
    }
}
