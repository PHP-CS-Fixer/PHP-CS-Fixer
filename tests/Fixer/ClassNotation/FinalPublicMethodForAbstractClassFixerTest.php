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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalPublicMethodForAbstractClassFixer
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

    public static function provideFixCases(): array
    {
        $original = $fixed = self::getClassElementStubs();
        $fixed = str_replace('public function f1', 'final public function f1', $fixed);
        $fixed = str_replace('public static function f4', 'final public static function f4', $fixed);
        $fixed = str_replace('static public function f7', 'final static public function f7', $fixed);

        return [
            'regular-class' => ["<?php class MyClass { {$original} }"],
            'final-class' => ["<?php final class MyClass { {$original} }"],
            'trait' => ["<?php trait MyClass { {$original} }"],
            'interface' => [
                '<?php interface MyClass {
                    public function f1();
                    public static function f4();
                    static public function f7();
                }',
            ],
            'magic-methods' => [
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
            ],
            'magic-methods-casing' => [
                '<?php abstract class MyClass {
                    public function __Construct() {}
                    public function __SET($a, $b) {}
                    public function __ToString() {}
                    public function __DeBuGiNfO() {}
                }',
            ],
            'non magic-methods' => [
                '<?php abstract class MyClass {
                    final public function __foo() {}
                    final public static function __bar($a, $b) {}
                }',
                '<?php abstract class MyClass {
                    public function __foo() {}
                    public static function __bar($a, $b) {}
                }',
            ],
            'abstract-class' => [
                "<?php abstract class MyClass { {$fixed} }",
                "<?php abstract class MyClass { {$original} }",
            ],
            'abstract-class-with-abstract-public-methods' => [
                '<?php abstract class MyClass {
                    abstract public function foo();
                    abstract public static function bar();
                }',
            ],
            'anonymous-class' => [
                sprintf(
                    '<?php abstract class MyClass { private function test() { $a = new class { %s }; } }',
                    self::getClassElementStubs()
                ),
            ],
            'constant visibility' => [
                '<?php abstract class MyClass {
                    public const A = 1;
                    protected const B = 2;
                    private const C = 3;
                }',
            ],
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
