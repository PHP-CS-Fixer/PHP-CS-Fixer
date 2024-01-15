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
            <<<'EOD'
                <?php class Foo
                            {
                                public function bar () {}
                            }
                EOD."\n            ",
        ];

        yield 'non-empty function body' => [
            <<<'EOD'
                <?php
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
                EOD."\n            ",
        ];

        yield 'classes' => [
            <<<'EOD'
                <?php
                            class Foo {}
                            class Bar extends BarParent {}
                            class Baz implements BazInterface {}
                            abstract class A {}
                            final class F {}
                EOD."\n            ",
            <<<'EOD'
                <?php
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
                EOD."\n            ",
        ];

        yield 'multiple functions' => [
            <<<'EOD'
                <?php
                                function notThis1()    { return 1; }
                                function f1() {}
                                function f2() {}
                                function f3() {}
                                function notThis2(){ return 1; }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function notThis1()    { return 1; }
                                function f1()
                                {}
                                function f2() {
                                }
                                function f3()
                                {
                                }
                                function notThis2(){ return 1; }
                EOD."\n            ",
        ];

        yield 'remove spaces' => [
            <<<'EOD'
                <?php
                                function f1() {}
                                function f2() {}
                                function f3() {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function f1() { }
                                function f2() {  }
                                function f3() {    }
                EOD."\n            ",
        ];

        yield 'add spaces' => [
            <<<'EOD'
                <?php
                                function f1() {}
                                function f2() {}
                                function f3() {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function f1(){}
                                function f2(){}
                                function f3(){}
                EOD."\n            ",
        ];

        yield 'with return types' => [
            <<<'EOD'
                <?php
                                function f1(): void {}
                                function f2(): \Foo\Bar {}
                                function f3(): ?string {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function f1(): void
                                {}
                                function f2(): \Foo\Bar    {    }
                                function f3(): ?string {


                                }
                EOD."\n            ",
        ];

        yield 'abstract functions' => [
            <<<'EOD'
                <?php abstract class C {
                                abstract function f1();
                                function f2() {}
                                abstract function f3();
                            }
                            if (true)    {    }
                EOD."\n            ",
            <<<'EOD'
                <?php abstract class C {
                                abstract function f1();
                                function f2()    {    }
                                abstract function f3();
                            }
                            if (true)    {    }
                EOD."\n            ",
        ];

        yield 'every token in separate line' => [
            <<<'EOD'
                <?php
                                function
                                foo
                                (
                                )
                                :
                                void {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                                function
                                foo
                                (
                                )
                                :
                                void
                                {
                                }
                EOD."\n            ",
        ];

        yield 'comments before body' => [
            <<<'EOD'
                <?php
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
                EOD."\n            ",
            <<<'EOD'
                <?php
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
                EOD."\n            ",
        ];

        yield 'anonymous class' => [
            <<<'EOD'
                <?php
                                $o = new class() {};
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $o = new class() {
                                };
                EOD."\n            ",
        ];

        yield 'anonymous function' => [
            <<<'EOD'
                <?php
                                $x = function () {};
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $x = function () {
                                };
                EOD."\n            ",
        ];

        yield 'interface' => [
            '<?php interface Foo {}'."\n            ",
            <<<'EOD'
                <?php interface Foo
                                {
                                }
                EOD."\n            ",
        ];

        yield 'trait' => [
            '<?php trait Foo {}'."\n            ",
            <<<'EOD'
                <?php trait Foo
                                {
                                }
                EOD."\n            ",
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
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(private int $x, private int $y) {}
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(private int $x, private int $y)
                                    {
                                    }
                                }
                EOD."\n            ",
        ];

        yield 'multi-line promoted properties' => [
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(
                                        private int $x,
                                        private int $y,
                                    ) {}
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(
                                        private int $x,
                                        private int $y,
                                    ) {
                                    }
                                }
                EOD."\n            ",
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
            <<<'EOD'
                <?php enum Foo
                                {
                                }
                EOD."\n            ",
        ];
    }
}
