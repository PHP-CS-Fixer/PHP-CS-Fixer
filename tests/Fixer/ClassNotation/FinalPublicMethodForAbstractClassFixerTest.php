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
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalPublicMethodForAbstractClassFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\FinalPublicMethodForAbstractClassFixer>
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FinalPublicMethodForAbstractClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected PHP source code
     * @param null|string $input    PHP source code
     *
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
        $original = $fixed = self::getClassElementStubs();
        $fixed = str_replace('public function f1', 'final public function f1', $fixed);
        $fixed = str_replace('public static function f4', 'final public static function f4', $fixed);
        $fixed = str_replace('static public function f7', 'final static public function f7', $fixed);

        yield 'regular-class' => ["<?php class MyClass { {$original} }"];

        yield 'final-class' => ["<?php final class MyClass { {$original} }"];

        yield 'trait' => ["<?php trait MyClass { {$original} }"];

        yield 'interface' => [
            '<?php interface MyClass {
                    public function f1();
                    public static function f4();
                    static public function f7();
                }',
        ];

        yield 'magic-methods' => [
            '<?php abstract class MyClass {
                    public function __construct() {}
                    public function __destruct() {}
                    public function __call($a, $b) {}
                    public static function __callStatic($a, $b) {}
                    public function __get($a) {}
                    public function __set($a, $b) {}
                    public function __isset($a) {}
                    public function __unset($a) {}
                    public function __sleep() {}
                    public function __wakeup() {}
                    public function __toString() {}
                    public function __invoke() {}
                    public function __clone() {}
                    public function __debugInfo() {}
                }',
        ];

        yield 'magic-methods-casing' => [
            '<?php abstract class MyClass {
                    public function __Construct() {}
                    public function __SET($a, $b) {}
                    public function __ToString() {}
                    public function __DeBuGiNfO() {}
                }',
        ];

        yield 'non magic-methods' => [
            '<?php abstract class MyClass {
                    final public function __foo() {}
                    final public static function __bar($a, $b) {}
                }',
            '<?php abstract class MyClass {
                    public function __foo() {}
                    public static function __bar($a, $b) {}
                }',
        ];

        yield 'abstract-class' => [
            "<?php abstract class MyClass { {$fixed} }",
            "<?php abstract class MyClass { {$original} }",
        ];

        yield 'abstract-class-with-abstract-public-methods' => [
            '<?php abstract class MyClass {
                    abstract public function foo();
                    abstract public static function bar();
                }',
        ];

        yield 'anonymous-class' => [
            \sprintf(
                '<?php abstract class MyClass { private function test() { $a = new class { %s }; } }',
                self::getClassElementStubs()
            ),
        ];

        yield 'constant visibility' => [
            '<?php abstract class MyClass {
                    public const A = 1;
                    protected const B = 2;
                    private const C = 3;
                }',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'abstract keyword after readonly/public keywords' => [
            '<?php readonly abstract class Foo {
                public abstract function bar();
            }',
        ];

        yield 'abstract keyword before readonly/public keywords' => [
            '<?php abstract readonly class Foo {
                abstract public function bar();
            }',
        ];

        yield 'abstract readonly class' => [
            '<?php abstract readonly class Foo {
                final public function bar() {}
            }',
            '<?php abstract readonly class Foo {
                public function bar() {}
            }',
        ];

        yield 'readonly abstract class' => [
            '<?php readonly abstract class Foo {
                final public function bar() {}
            }',
            '<?php readonly abstract class Foo {
                public function bar() {}
            }',
        ];
    }

    private static function getClassElementStubs(): string
    {
        return '
            public $a1;
            protected $a2;
            private $a3;
            public static $a4;
            protected static $a5;
            private static $a6;
            public function f1(){}
            protected function f2(){}
            private function f3(){}
            public static function f4(){}
            protected static function f5(){}
            private static function f6(){}
            static public function f7(){}
            static protected function f8(){}
            static private function f9(){}
        ';
    }
}
