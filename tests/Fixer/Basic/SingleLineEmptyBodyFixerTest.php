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
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'non-empty class' => [
            '<?php class Foo
            {
                public function bar () {}
            }
            ',
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
                }
            ',
        ];

        yield 'classes' => [
            '<?php
            class Foo {}
            class Bar extends BarParent {}
            class Baz implements BazInterface {}
            abstract class A {}
            final class F {}
            ',
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

            }
            ',
        ];

        yield 'multiple functions' => [
            '<?php
                function notThis1()    { return 1; }
                function f1() {}
                function f2() {}
                function f3() {}
                function notThis2(){ return 1; }
            ',
            '<?php
                function notThis1()    { return 1; }
                function f1()
                {}
                function f2() {
                }
                function f3()
                {
                }
                function notThis2(){ return 1; }
            ',
        ];

        yield 'remove spaces' => [
            '<?php
                function f1() {}
                function f2() {}
                function f3() {}
            ',
            '<?php
                function f1() { }
                function f2() {  }
                function f3() {    }
            ',
        ];

        yield 'add spaces' => [
            '<?php
                function f1() {}
                function f2() {}
                function f3() {}
            ',
            '<?php
                function f1(){}
                function f2(){}
                function f3(){}
            ',
        ];

        yield 'with return types' => [
            '<?php
                function f1(): void {}
                function f2(): \Foo\Bar {}
                function f3(): ?string {}
            ',
            '<?php
                function f1(): void
                {}
                function f2(): \Foo\Bar    {    }
                function f3(): ?string {


                }
            ',
        ];

        yield 'abstract functions' => [
            '<?php abstract class C {
                abstract function f1();
                function f2() {}
                abstract function f3();
            }
            if (true)    {    }
            ',
            '<?php abstract class C {
                abstract function f1();
                function f2()    {    }
                abstract function f3();
            }
            if (true)    {    }
            ',
        ];

        yield 'every token in separate line' => [
            '<?php
                function
                foo
                (
                )
                :
                void {}
            ',
            '<?php
                function
                foo
                (
                )
                :
                void
                {
                }
            ',
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
                {}
            ',
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
                {    }
            ',
        ];

        yield 'anonymous class' => [
            '<?php
                $o = new class() {};
            ',
            '<?php
                $o = new class() {
                };
            ',
        ];

        yield 'anonymous function' => [
            '<?php
                $x = function () {};
            ',
            '<?php
                $x = function () {
                };
            ',
        ];

        yield 'interface' => [
            '<?php interface Foo {}
            ',
            '<?php interface Foo
                {
                }
            ',
        ];

        yield 'trait' => [
            '<?php trait Foo {}
            ',
            '<?php trait Foo
                {
                }
            ',
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
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'single-line promoted properties' => [
            '<?php class Foo
                {
                    public function __construct(private int $x, private int $y) {}
                }
            ',
            '<?php class Foo
                {
                    public function __construct(private int $x, private int $y)
                    {
                    }
                }
            ',
        ];

        yield 'multi-line promoted properties' => [
            '<?php class Foo
                {
                    public function __construct(
                        private int $x,
                        private int $y,
                    ) {}
                }
            ',
            '<?php class Foo
                {
                    public function __construct(
                        private int $x,
                        private int $y,
                    ) {
                    }
                }
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
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php enum Foo {}
            ',
            '<?php enum Foo
                {
                }
            ',
        ];
    }
}
