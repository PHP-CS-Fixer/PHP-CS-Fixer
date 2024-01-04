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
            <<<'EOD'
                <?php
                namespace Test;
                echo FOO, \Bar\BAZ, namespace\FOO2;
                EOD
        ];

        yield 'name already used [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                echo \FOO, FOO, \FOO;
                EOD
        ];

        yield 'name already used [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const Bar\FOO;
                echo \FOO;
                EOD
        ];

        yield 'name already used [3]' => [
            <<<'EOD'
                <?php
                namespace Test;
                const FOO = 1;
                echo \FOO;
                EOD
        ];

        yield 'without namespace / do not import' => [
            <<<'EOD'
                <?php
                echo \FOO, \BAR, \FOO;
                EOD
        ];

        yield 'with namespace' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const BAR;
                use const FOO;
                echo FOO, BAR;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                echo \FOO, \BAR;
                EOD
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EOD'
                <?php
                namespace Test {
                use const BAR;
                use const FOO;
                    echo FOO, BAR;
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test {
                    echo \FOO, \BAR;
                }
                EOD
        ];

        yield 'ignore other imported types' => [
            <<<'EOD'
                <?php
                namespace Test;
                use BAR;
                use const BAR;
                use const FOO;
                echo FOO, BAR;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use BAR;
                echo \FOO, \BAR;
                EOD
        ];

        yield 'respect already imported names [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const BAR;
                use const FOO;
                echo FOO, BAR;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use const BAR;
                echo \FOO, \BAR;
                EOD
        ];

        yield 'respect already imported names [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const \BAR;
                use const FOO;
                echo FOO, BAR, BAR;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use const \BAR;
                echo \FOO, \BAR, BAR;
                EOD
        ];

        yield 'handle case sensitivity' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const fOO;
                use const FOO;
                use const Foo;
                const foO = 1;
                echo FOO, Foo;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use const fOO;
                const foO = 1;
                echo \FOO, \Foo;
                EOD
        ];

        yield 'handle aliased imports' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const BAR as BAZ;
                use const FOO;
                echo FOO, BAZ;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use const BAR as BAZ;
                echo \FOO, \BAR;
                EOD
        ];

        yield 'ignore class constants' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const FOO;
                class Bar {
                    const FOO = 1;
                }
                echo FOO;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                class Bar {
                    const FOO = 1;
                }
                echo \FOO;
                EOD
        ];

        yield 'global namespace' => [
            <<<'EOD'
                <?php
                echo \FOO, \BAR;
                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                namespace {
                    echo \FOO, \BAR;
                }
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                foo();
                Bar\baz();
                namespace\foo2();
                EOD
        ];

        yield 'name already used [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                \foo();
                Foo();
                \foo();
                EOD
        ];

        yield 'name already used [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function Bar\foo;
                \Foo();
                EOD
        ];

        yield 'name already used [3]' => [
            <<<'EOD'
                <?php
                namespace Test;
                function foo() {}
                \Foo();
                EOD
        ];

        yield 'without namespace / do not import' => [
            <<<'EOD'
                <?php
                \foo();
                \bar();
                \Foo();
                EOD
        ];

        yield 'with namespace' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function bar;
                use function foo;
                foo();
                bar();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                \foo();
                \bar();
                EOD
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EOD'
                <?php
                namespace Test {
                use function bar;
                use function foo;
                    foo();
                    bar();
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test {
                    \foo();
                    \bar();
                }
                EOD
        ];

        yield 'ignore other imported types' => [
            <<<'EOD'
                <?php
                namespace Test;
                use bar;
                use function bar;
                use function foo;
                foo();
                bar();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use bar;
                \foo();
                \bar();
                EOD
        ];

        yield 'respect already imported names [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function bar;
                use function foo;
                foo();
                Bar();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use function bar;
                \foo();
                \Bar();
                EOD
        ];

        yield 'respect already imported names [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function \bar;
                use function foo;
                foo();
                Bar();
                bar();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use function \bar;
                \foo();
                \Bar();
                bar();
                EOD
        ];

        yield 'handle aliased imports' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function bar as baz;
                use function foo;
                foo();
                baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use function bar as baz;
                \foo();
                \Bar();
                EOD
        ];

        yield 'ignore class methods' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function foo;
                class Bar {
                    function foo() {}
                }
                foo();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                class Bar {
                    function foo() {}
                }
                \foo();
                EOD
        ];

        yield 'name already used' => [
            <<<'EOD'
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
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                new Foo();
                new Bar\Baz();
                new namespace\Foo2();

                /** @var Foo|Bar\Baz $x */
                $x = x();
                EOD
        ];

        yield 'name already used [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                new \Foo();
                new foo();

                /** @var \Foo $foo */
                $foo = new \Foo();
                EOD
        ];

        yield 'name already used [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar\foo;

                /** @var \Foo $foo */
                $foo = new \Foo();
                EOD
        ];

        yield 'name already used [3]' => [
            <<<'EOD'
                <?php
                namespace Test;
                class foo {}

                /** @var \Foo $foo */
                $foo = new \Foo();
                EOD
        ];

        yield 'name already used [4]' => [
            <<<'EOD'
                <?php
                namespace Test;

                /** @return array<string, foo> */
                function x() {}

                /** @var \Foo $foo */
                $foo = new \Foo();
                EOD
        ];

        yield 'without namespace / do not import' => [
            <<<'EOD'
                <?php
                /** @var \Foo $foo */
                $foo = new \foo();
                new \Bar();
                \FOO::baz();
                EOD
        ];

        yield 'with namespace' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar;
                use Baz;
                use Foo;

                new Foo();
                Bar::baz();

                /** @return Baz<string, foo> */
                function x() {}
                EOD,
            <<<'EOD'
                <?php
                namespace Test;

                new \Foo();
                \Bar::baz();

                /** @return \Baz<string, \foo> */
                function x() {}
                EOD
        ];

        yield 'with namespace with {} syntax' => [
            <<<'EOD'
                <?php
                namespace Test {
                use Bar;
                use Foo;
                    new Foo();
                    Bar::baz();
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test {
                    new \Foo();
                    \Bar::baz();
                }
                EOD
        ];

        yield 'phpdoc only' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}
                EOD,
            <<<'EOD'
                <?php
                namespace Test;

                /** @throws \Throwable */
                function x() {}
                EOD
        ];

        yield 'ignore other imported types' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function Bar;
                use Bar;
                use Foo;
                new Foo();
                Bar::baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use function Bar;
                new \Foo();
                \Bar::baz();
                EOD
        ];

        yield 'respect already imported names [1]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar;
                use Foo;
                new Foo();
                bar::baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Bar;
                new \Foo();
                \bar::baz();
                EOD
        ];

        yield 'respect already imported names [2]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use \Bar;
                use Foo;
                new Foo();
                new bar();
                new Bar();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use \Bar;
                new \Foo();
                new \bar();
                new Bar();
                EOD
        ];

        yield 'respect already imported names [3]' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}

                /** @throws Throwable */
                function y() {}
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Throwable;

                /** @throws Throwable */
                function x() {}

                /** @throws \Throwable */
                function y() {}
                EOD
        ];

        yield 'handle aliased imports' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar as Baz;
                use Foo;

                new Foo();

                /** @var Baz $bar */
                $bar = new Baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Bar as Baz;

                new \Foo();

                /** @var \bar $bar */
                $bar = new \bar();
                EOD
        ];

        yield 'handle typehints' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar;
                use Baz;
                use Foo;
                class Abc {
                    function bar(Foo $a, Bar $b, foo &$c, Baz ...$d) {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                class Abc {
                    function bar(\Foo $a, \Bar $b, \foo &$c, \Baz ...$d) {}
                }
                EOD
        ];

        yield 'handle typehints 2' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Bar;
                use Foo;
                class Abc {
                    function bar(?Foo $a): ?Bar {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                class Abc {
                    function bar(?\Foo $a): ?\Bar {}
                }
                EOD
        ];

        yield 'try catch' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception $e) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                try {
                } catch (\Exception $e) {
                }
                EOD
        ];

        yield 'try catch with comments' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ Exception $e /* ... */) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                try {
                } catch (/* ... */ \Exception $e /* ... */) {
                }
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                try {
                } catch (\Exception) {
                }
                EOD
        ];

        yield 'try catch without variable and comments' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ Exception /* just because! */) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                try {
                } catch (/* non-capturing catch */ \Exception /* just because! */) {
                }
                EOD
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
            <<<'EOD'
                <?php
                use const FOO;
                use const BAR;
                echo \FOO, Baz\BAR;
                EOD
        ];

        yield 'handle all occurrences' => [
            <<<'EOD'
                <?php
                namespace X;
                use const FOO;
                use const BAR;
                echo \FOO, \BAR, \FOO;
                EOD,
            <<<'EOD'
                <?php
                namespace X;
                use const FOO;
                use const BAR;
                echo FOO, BAR, FOO;
                EOD
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EOD'
                <?php
                namespace Test;
                use FOO;
                use const BAR;
                use const Baz;
                echo FOO, \BAR, BAZ, QUX;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use FOO;
                use const BAR;
                use const Baz;
                echo FOO, BAR, BAZ, QUX;
                EOD
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
            <<<'EOD'
                <?php
                use function foo;
                use function bar;
                \foo();
                Baz\bar();
                EOD
        ];

        yield 'handle all occurrences' => [
            <<<'EOD'
                <?php
                namespace X;
                use function foo;
                use function bar;
                \foo();
                \bar();
                \Foo();
                EOD,
            <<<'EOD'
                <?php
                namespace X;
                use function foo;
                use function bar;
                foo();
                bar();
                Foo();
                EOD
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EOD'
                <?php
                namespace Test;
                use foo;
                use function bar;
                foo();
                \bar();
                baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use foo;
                use function bar;
                foo();
                bar();
                baz();
                EOD
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
            <<<'EOD'
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
                EOD
        ];

        yield 'handle all occurrences' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'ignore other imports and non-imported names' => [
            <<<'EOD'
                <?php
                namespace Test;
                use function Foo;
                use Bar;
                new Foo();
                new \Bar();
                new Baz();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use function Foo;
                use Bar;
                new Foo();
                new Bar();
                new Baz();
                EOD
        ];

        yield 'try catch' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (\Exception $e) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception $e) {
                }
                EOD
        ];

        yield 'try catch with comments' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ \Exception $e /* ... */) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* ... */ Exception $e /* ... */) {
                }
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (\Exception) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (Exception) {
                }
                EOD
        ];

        yield 'try catch without variable and comments' => [
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ \Exception /* just because! */) {
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                use Exception;
                try {
                } catch (/* non-capturing catch */ Exception /* just because! */) {
                }
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                echo \FOO, \BAR;

                namespace OtherTest;
                echo \FOO, \BAR;
                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Test {
                    echo \FOO, \BAR;

                }

                namespace OtherTest {
                    echo \FOO, \BAR;
                }
                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                namespace {
                    echo \FOO, \BAR;

                }

                namespace OtherTest {
                    echo \FOO, \BAR;
                }
                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Test {
                    echo \FOO, \BAR;

                }

                namespace {
                    echo \FOO, \BAR;
                }
                EOD
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
            <<<'EOD'
                <?php
                namespace Test;
                use function foo;
                enum Bar {
                    function foo() {}
                }
                foo();
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                enum Bar {
                    function foo() {}
                }
                \foo();
                EOD
        ];

        yield 'ignore enum constants' => [
            <<<'EOD'
                <?php
                namespace Test;
                use const FOO;
                enum Bar {
                    const FOO = 1;
                }
                echo FOO;
                EOD,
            <<<'EOD'
                <?php
                namespace Test;
                enum Bar {
                    const FOO = 1;
                }
                echo \FOO;
                EOD
        ];
    }
}
