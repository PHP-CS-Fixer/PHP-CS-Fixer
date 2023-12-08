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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer
 */
final class GlobalNamespaceImportFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixImportConstantsCases
     */
    public function testFixImportConstants(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_constants' => true]);
        $this->doTest($expected, $input);
    }

    public static function provideFixImportConstantsCases(): iterable
    {
        yield 'non-global names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                echo FOO, \Bar\BAZ, namespace\FOO2;
                EXPECTED
        ];

        yield 'name already used [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                echo \FOO, FOO, \FOO;
                EXPECTED
        ];

        yield 'name already used [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const Bar\FOO;
                echo \FOO;
                EXPECTED
        ];

        yield 'name already used [3]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                const FOO = 1;
                echo \FOO;
                EXPECTED
        ];

        yield 'without namespace / do not import' => [
            <<<'INPUT'
                <?php
                echo \FOO, \BAR, \FOO;
                INPUT
        ];

        yield 'with namespace' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const FOO;
                use const BAR;
                echo FOO, BAR;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                echo \FOO, \BAR;
                INPUT
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EXPECTED'
                <?php
                namespace Test {
                use const FOO;
                use const BAR;
                    echo FOO, BAR;
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test {
                    echo \FOO, \BAR;
                }
                INPUT
        ];

        yield 'ignore other imported types' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use BAR;
                use const FOO;
                use const BAR;
                echo FOO, BAR;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use BAR;
                echo \FOO, \BAR;
                INPUT
        ];

        yield 'respect already imported names [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const BAR;
                use const FOO;
                echo FOO, BAR;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use const BAR;
                echo \FOO, \BAR;
                INPUT
        ];

        yield 'respect already imported names [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const \BAR;
                use const FOO;
                echo FOO, BAR, BAR;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use const \BAR;
                echo \FOO, \BAR, BAR;
                INPUT
        ];

        yield 'handle case sensitivity' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const fOO;
                use const FOO;
                use const Foo;
                const foO = 1;
                echo FOO, Foo;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use const fOO;
                const foO = 1;
                echo \FOO, \Foo;
                INPUT
        ];

        yield 'handle aliased imports' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const BAR as BAZ;
                use const FOO;
                echo FOO, BAZ;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use const BAR as BAZ;
                echo \FOO, \BAR;
                INPUT
        ];

        yield 'ignore class constants' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const FOO;
                class Bar {
                    const FOO = 1;
                }
                echo FOO;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                class Bar {
                    const FOO = 1;
                }
                echo \FOO;
                INPUT
        ];

        yield 'global namespace' => [
            <<<'INPUT'
                <?php
                echo \FOO, \BAR;
                INPUT
        ];

        yield [
            <<<'INPUT'
                <?php
                namespace {
                    echo \FOO, \BAR;
                }
                INPUT
        ];
    }

    /**
     * @dataProvider provideFixImportFunctionsCases
     */
    public function testFixImportFunctions(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_functions' => true]);
        $this->doTest($expected, $input);
    }

    public static function provideFixImportFunctionsCases(): iterable
    {
        yield 'non-global names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                foo();
                Bar\baz();
                namespace\foo2();
                EXPECTED
        ];

        yield 'name already used [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                \foo();
                Foo();
                \foo();
                EXPECTED
        ];

        yield 'name already used [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function Bar\foo;
                \Foo();
                EXPECTED
        ];

        yield 'name already used [3]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                function foo() {}
                \Foo();
                EXPECTED
        ];

        yield 'without namespace / do not import' => [
            <<<'INPUT'
                <?php
                \foo();
                \bar();
                \Foo();
                INPUT
        ];

        yield 'with namespace' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function foo;
                use function bar;
                foo();
                bar();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                \foo();
                \bar();
                INPUT
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EXPECTED'
                <?php
                namespace Test {
                use function foo;
                use function bar;
                    foo();
                    bar();
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test {
                    \foo();
                    \bar();
                }
                INPUT
        ];

        yield 'ignore other imported types' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use bar;
                use function foo;
                use function bar;
                foo();
                bar();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use bar;
                \foo();
                \bar();
                INPUT
        ];

        yield 'respect already imported names [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function bar;
                use function foo;
                foo();
                Bar();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use function bar;
                \foo();
                \Bar();
                INPUT
        ];

        yield 'respect already imported names [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function \bar;
                use function foo;
                foo();
                Bar();
                bar();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use function \bar;
                \foo();
                \Bar();
                bar();
                INPUT
        ];

        yield 'handle aliased imports' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function bar as baz;
                use function foo;
                foo();
                baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use function bar as baz;
                \foo();
                \Bar();
                INPUT
        ];

        yield 'ignore class methods' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function foo;
                class Bar {
                    function foo() {}
                }
                foo();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                class Bar {
                    function foo() {}
                }
                \foo();
                INPUT
        ];

        yield 'name already used' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                class Bar {
                    function baz() {
                        new class() {
                            function baz() {
                                function foo() {}
                            }
                        };
                    }
                }
                \foo();
                EXPECTED
        ];
    }

    /**
     * @dataProvider provideFixImportClassesCases
     */
    public function testFixImportClasses(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_classes' => true]);
        $this->doTest($expected, $input);
    }

    public static function provideFixImportClassesCases(): iterable
    {
        yield 'non-global names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                new Foo();
                new Bar\Baz();
                new namespace\Foo2();

                /** @var Foo|Bar\Baz $x */
                $x = x();
                EXPECTED
        ];

        yield 'name already used [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                new \Foo();
                new foo();

                /** @var \Foo $foo */
                $foo = new \Foo();
                EXPECTED
        ];

        yield 'name already used [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Bar\foo;

                /** @var \Foo $foo */
                $foo = new \Foo();
                EXPECTED
        ];

        yield 'name already used [3]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                class foo {}

                /** @var \Foo $foo */
                $foo = new \Foo();
                EXPECTED
        ];

        yield 'name already used [4]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;

                /** @return array<string, foo> */
                function x() {}

                /** @var \Foo $foo */
                $foo = new \Foo();
                EXPECTED
        ];

        yield 'without namespace / do not import' => [
            <<<'INPUT'
                <?php
                /** @var \Foo $foo */
                $foo = new \foo();
                new \Bar();
                \FOO::baz();
                INPUT
        ];

        yield 'with namespace' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Bar;
                use Baz;
                use foo;

                new Foo();
                Bar::baz();

                /** @return Baz<string, foo> */
                function x() {}
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;

                new \Foo();
                \Bar::baz();

                /** @return \Baz<string, \foo> */
                function x() {}
                INPUT
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EXPECTED'
                <?php
                namespace Test {
                use Foo;
                use Bar;
                    new Foo();
                    Bar::baz();
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test {
                    new \Foo();
                    \Bar::baz();
                }
                INPUT
        ];

        yield 'phpdoc only' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;

                /** @throws \Throwable */
                function x() {}
                INPUT
        ];

        yield 'ignore other imported types' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function Bar;
                use Foo;
                use Bar;
                new Foo();
                Bar::baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use function Bar;
                new \Foo();
                \Bar::baz();
                INPUT
        ];

        yield 'respect already imported names [1]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Bar;
                use Foo;
                new Foo();
                bar::baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Bar;
                new \Foo();
                \bar::baz();
                INPUT
        ];

        yield 'respect already imported names [2]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use \Bar;
                use Foo;
                new Foo();
                new bar();
                new Bar();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use \Bar;
                new \Foo();
                new \bar();
                new Bar();
                INPUT
        ];

        yield 'respect already imported names [3]' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}

                /** @throws Throwable */
                function y() {}
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}

                /** @throws \Throwable */
                function y() {}
                INPUT
        ];

        yield 'handle aliased imports' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Bar as Baz;
                use Foo;

                new Foo();

                /** @var Baz $bar */
                $bar = new Baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Bar as Baz;

                new \Foo();

                /** @var \bar $bar */
                $bar = new \bar();
                INPUT
        ];

        yield 'handle typehints' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Bar;
                use Foo;
                use Baz;
                class Abc {
                    function bar(Foo $a, Bar $b, foo &$c, Baz ...$d) {}
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                class Abc {
                    function bar(\Foo $a, \Bar $b, \foo &$c, \Baz ...$d) {}
                }
                INPUT
        ];

        yield 'handle typehints 2' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Foo;
                use Bar;
                class Abc {
                    function bar(?Foo $a): ?Bar {}
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                class Abc {
                    function bar(?\Foo $a): ?\Bar {}
                }
                INPUT
        ];

        yield 'try catch' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception $e) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                try {
                } catch (\Exception $e) {
                }
                INPUT
        ];

        yield 'try catch with comments' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ Exception $e /* ... */) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                try {
                } catch (/* ... */ \Exception $e /* ... */) {
                }
                INPUT
        ];
    }

    /**
     * @dataProvider provideFixImportClasses80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixImportClasses80(string $expected, string $input): void
    {
        $this->fixer->configure(['import_classes' => true]);
        $this->doTest($expected, $input);
    }

    public static function provideFixImportClasses80Cases(): iterable
    {
        yield 'try catch without variable' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                try {
                } catch (\Exception) {
                }
                INPUT
        ];

        yield 'try catch without variable and comments' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ Exception /* just because! */) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                try {
                } catch (/* non-capturing catch */ \Exception /* just because! */) {
                }
                INPUT
        ];
    }

    /**
     * @dataProvider provideFixFullyQualifyConstantsCases
     */
    public function testFixFullyQualifyConstants(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_constants' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixFullyQualifyConstantsCases(): iterable
    {
        yield 'already fqn or sub namespace' => [
            <<<'EXPECTED'
                <?php
                use const FOO;
                use const BAR;
                echo \FOO, Baz\BAR;
                EXPECTED
        ];

        yield 'handle all occurrences' => [
            <<<'EXPECTED'
                <?php
                namespace X;
                use const FOO;
                use const BAR;
                echo \FOO, \BAR, \FOO;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace X;
                use const FOO;
                use const BAR;
                echo FOO, BAR, FOO;
                INPUT
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use FOO;
                use const BAR;
                use const Baz;
                echo FOO, \BAR, BAZ, QUX;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use FOO;
                use const BAR;
                use const Baz;
                echo FOO, BAR, BAZ, QUX;
                INPUT
        ];
    }

    /**
     * @dataProvider provideFixFullyQualifyFunctionsCases
     */
    public function testFixFullyQualifyFunctions(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_functions' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixFullyQualifyFunctionsCases(): iterable
    {
        yield 'already fqn or sub namespace' => [
            <<<'EXPECTED'
                <?php
                use function foo;
                use function bar;
                \foo();
                Baz\bar();
                EXPECTED
        ];

        yield 'handle all occurrences' => [
            <<<'EXPECTED'
                <?php
                namespace X;
                use function foo;
                use function bar;
                \foo();
                \bar();
                \Foo();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace X;
                use function foo;
                use function bar;
                foo();
                bar();
                Foo();
                INPUT
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use foo;
                use function bar;
                foo();
                \bar();
                baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use foo;
                use function bar;
                foo();
                bar();
                baz();
                INPUT
        ];
    }

    /**
     * @dataProvider provideFixFullyQualifyClassesCases
     */
    public function testFixFullyQualifyClasses(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['import_classes' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixFullyQualifyClassesCases(): iterable
    {
        yield 'already fqn or sub namespace' => [
            <<<'EXPECTED'
                <?php
                use Foo;
                use Bar;

                new \Foo();
                Baz\Bar::baz();

                /**
                 * @param \Foo $foo
                 * @param Baz\Bar $bar
                 */
                function abc(\Foo $foo, Baz\Bar $bar = null) {}
                EXPECTED
        ];

        yield 'handle all occurrences' => [
            <<<'EXPECTED'
                <?php
                namespace X;
                use Foo;
                use Bar;

                new \Foo();
                new \Bar();
                \foo::baz();

                /**
                 * @param \Foo|string $foo
                 * @param null|\Bar[] $bar
                 * @return array<string, ?\Bar<int, \foo>>|null
                 */
                function abc($foo, \Bar $bar = null) {}
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace X;
                use Foo;
                use Bar;

                new Foo();
                new Bar();
                foo::baz();

                /**
                 * @param Foo|string $foo
                 * @param null|Bar[] $bar
                 * @return array<string, ?Bar<int, foo>>|null
                 */
                function abc($foo, Bar $bar = null) {}
                INPUT
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function Foo;
                use Bar;
                new Foo();
                new \Bar();
                new Baz();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use function Foo;
                use Bar;
                new Foo();
                new Bar();
                new Baz();
                INPUT
        ];

        yield 'try catch' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (\Exception $e) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception $e) {
                }
                INPUT
        ];

        yield 'try catch with comments' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ \Exception $e /* ... */) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ Exception $e /* ... */) {
                }
                INPUT
        ];

        yield 'key in PHPDoc\'s array shape matching class name' => [
            '<?php
                namespace Foo;
                use Exception;
                class Bar {
                    /**
                     * @return array{code: int, exception: \Exception}
                     */
                    public function f1(): array {}
                    /**
                     * @return array{exception: \Exception}
                     */
                    public function f2(): array {}
                    /**
                     * @return array{exceptions: array<\Exception>}
                     */
                    public function f3(): array {}
                }',
            '<?php
                namespace Foo;
                use Exception;
                class Bar {
                    /**
                     * @return array{code: int, exception: Exception}
                     */
                    public function f1(): array {}
                    /**
                     * @return array{exception: Exception}
                     */
                    public function f2(): array {}
                    /**
                     * @return array{exceptions: array<Exception>}
                     */
                    public function f3(): array {}
                }',
        ];
    }

    /**
     * @dataProvider provideFixFullyQualifyClasses80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixFullyQualifyClasses80(string $expected, string $input): void
    {
        $this->fixer->configure(['import_classes' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixFullyQualifyClasses80Cases(): iterable
    {
        yield 'try catch without variable' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (\Exception) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception) {
                }
                INPUT
        ];

        yield 'try catch without variable and comments' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ \Exception /* just because! */) {
                }
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ Exception /* just because! */) {
                }
                INPUT
        ];
    }

    /**
     * @dataProvider provideMultipleNamespacesCases
     */
    public function testMultipleNamespaces(string $expected): void
    {
        $this->fixer->configure(['import_constants' => true]);
        $this->doTest($expected);
    }

    public static function provideMultipleNamespacesCases(): iterable
    {
        yield [
            <<<'INPUT'
                <?php
                namespace Test;
                echo \FOO, \BAR;

                namespace OtherTest;
                echo \FOO, \BAR;
                INPUT
        ];

        yield [
            <<<'INPUT'
                <?php
                namespace Test {
                    echo \FOO, \BAR;

                }

                namespace OtherTest {
                    echo \FOO, \BAR;
                }
                INPUT
        ];

        yield [
            <<<'INPUT'
                <?php
                namespace {
                    echo \FOO, \BAR;

                }

                namespace OtherTest {
                    echo \FOO, \BAR;
                }
                INPUT
        ];

        yield [
            <<<'INPUT'
                <?php
                namespace Test {
                    echo \FOO, \BAR;

                }

                namespace {
                    echo \FOO, \BAR;
                }
                INPUT
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testAttributes(): void
    {
        $this->fixer->configure([
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ]);
        $this->doTest(
            '<?php
namespace Foo;
use AnAttribute1;
use AnAttribute2;
use AnAttribute3;
class Bar
{
    #[AnAttribute1]
    public function f1() {}
    #[AnAttribute2, AnAttribute3]
    public function f2() {}
}',
            '<?php
namespace Foo;
class Bar
{
    #[\AnAttribute1]
    public function f1() {}
    #[\AnAttribute2, \AnAttribute3]
    public function f2() {}
}'
        );
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'import_constants' => true,
            'import_functions' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'ignore enum methods' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use function foo;
                enum Bar {
                    function foo() {}
                }
                foo();
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                enum Bar {
                    function foo() {}
                }
                \foo();
                INPUT
        ];

        yield 'ignore enum constants' => [
            <<<'EXPECTED'
                <?php
                namespace Test;
                use const FOO;
                enum Bar {
                    const FOO = 1;
                }
                echo FOO;
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                namespace Test;
                enum Bar {
                    const FOO = 1;
                }
                echo \FOO;
                INPUT
        ];
    }
}
