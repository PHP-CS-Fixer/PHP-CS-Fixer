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
 * @covers \PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer
 */
final class SingleLineEmptyBodyFixerTest extends AbstractFixerTestCase
{
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
        yield 'non-empty class' => [
            '<?php class Foo
            {
                public function bar () {}
            }'."\n            ",
        ];

        yield 'non-empty function body' => [
            '<?php
                function f1()
                { /* foo */ }
                function f2()
                { /** foo */ }
                function f3()
                { // foo
                }
                function f4()
                {
                    return true;
                }'."\n            ",
        ];

        yield 'classes' => [
            '<?php
            class Foo {}
            class Bar extends BarParent {}
            class Baz implements BazInterface {}
            abstract class A {}
            final class F {}'."\n            ",
            '<?php
            class Foo
            {
            }
            class Bar extends BarParent
            {}
            class Baz implements BazInterface    {}
            abstract class A
            {}
            final class F
            {

            }'."\n            ",
        ];

        yield 'multiple functions' => [
            '<?php
                function notThis1()    { return 1; }
                function f1() {}
                function f2() {}
                function f3() {}
                function notThis2(){ return 1; }'."\n            ",
            '<?php
                function notThis1()    { return 1; }
                function f1()
                {}
                function f2() {
                }
                function f3()
                {
                }
                function notThis2(){ return 1; }'."\n            ",
        ];

        yield 'remove spaces' => [
            '<?php
                function f1() {}
                function f2() {}
                function f3() {}'."\n            ",
            '<?php
                function f1() { }
                function f2() {  }
                function f3() {    }'."\n            ",
        ];

        yield 'add spaces' => [
            '<?php
                function f1() {}
                function f2() {}
                function f3() {}'."\n            ",
            '<?php
                function f1(){}
                function f2(){}
                function f3(){}'."\n            ",
        ];

        yield 'with return types' => [
            '<?php
                function f1(): void {}
                function f2(): \Foo\Bar {}
                function f3(): ?string {}'."\n            ",
            '<?php
                function f1(): void
                {}
                function f2(): \Foo\Bar    {    }
                function f3(): ?string {


                }'."\n            ",
        ];

        yield 'abstract functions' => [
            '<?php abstract class C {
                abstract function f1();
                function f2() {}
                abstract function f3();
            }
            if (true)    {    }'."\n            ",
            '<?php abstract class C {
                abstract function f1();
                function f2()    {    }
                abstract function f3();
            }
            if (true)    {    }'."\n            ",
        ];

        yield 'every token in separate line' => [
            '<?php
                function
                foo
                (
                )
                :
                void {}'."\n            ",
            '<?php
                function
                foo
                (
                )
                :
                void
                {
                }'."\n            ",
        ];

        yield 'comments before body' => [
            '<?php
                function f1()
                // foo
                {}
                function f2()
                /* foo */
                {}
                function f3()
                /** foo */
                {}
                function f4()
                /** foo */
                /** bar */
                {}'."\n            ",
            '<?php
                function f1()
                // foo
                {
                }
                function f2()
                /* foo */
                {

                }
                function f3()
                /** foo */
                {
                }
                function f4()
                /** foo */
                /** bar */
                {    }'."\n            ",
        ];

        yield 'anonymous class' => [
            '<?php
                $o = new class() {};'."\n            ",
            '<?php
                $o = new class() {
                };'."\n            ",
        ];

        yield 'anonymous function' => [
            '<?php
                $x = function () {};'."\n            ",
            '<?php
                $x = function () {
                };'."\n            ",
        ];

        yield 'interface' => [
            '<?php interface Foo {}'."\n            ",
            '<?php interface Foo
                {
                }'."\n            ",
        ];

        yield 'trait' => [
            '<?php trait Foo {}'."\n            ",
            '<?php trait Foo
                {
                }'."\n            ",
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'single-line promoted properties' => [
            '<?php class Foo
                {
                    public function __construct(private int $x, private int $y) {}
                }'."\n            ",
            '<?php class Foo
                {
                    public function __construct(private int $x, private int $y)
                    {
                    }
                }'."\n            ",
        ];

        yield 'multi-line promoted properties' => [
            '<?php class Foo
                {
                    public function __construct(
                        private int $x,
                        private int $y,
                    ) {}
                }'."\n            ",
            '<?php class Foo
                {
                    public function __construct(
                        private int $x,
                        private int $y,
                    ) {
                    }
                }'."\n            ",
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
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php enum Foo {}'."\n            ",
            '<?php enum Foo
                {
                }'."\n            ",
        ];
    }
}
