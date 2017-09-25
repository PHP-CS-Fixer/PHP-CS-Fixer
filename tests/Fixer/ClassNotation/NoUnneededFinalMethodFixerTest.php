<?php

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
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer
 */
final class NoUnneededFinalMethodFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'default' => [
                '<?php
final class Foo {
    public function foo() {}
    protected function bar() {}
    private function baz() {}
}',
                '<?php
final class Foo {
    final public function foo() {}
    final protected function bar() {}
    final private function baz() {}
}',
            ],
            'final-after-visibility' => [
                '<?php
final class Foo {
    public function foo() {}
    protected function bar() {}
    private function baz() {}
}',
                '<?php
final class Foo {
    public final function foo() {}
    protected final function bar() {}
    private final function baz() {}
}',
            ],
            'default-static' => [
                '<?php
final class SomeClass {
    public static function foo() {}
    protected static function bar() {}
    private static function baz() {}
}',
                '<?php
final class SomeClass {
    final public static function foo() {}
    final protected static function bar() {}
    final private static function baz() {}
}',
            ],
            'visibility-then-final-then-static' => [
                '<?php
final class SomeClass {
    public static function foo() {}
    protected static function bar() {}
    private static function baz() {}
}',
                '<?php
final class SomeClass {
    public final static function foo() {}
    protected final static function bar() {}
    private final static function baz() {}
}',
            ],
            'visibility-then-static-then-final' => [
                '<?php
final class SomeClass {
    public static function foo() {}
    protected static function bar() {}
    private static function baz() {}
}',
                '<?php
final class SomeClass {
    public static final function foo() {}
    protected static final function bar() {}
    private static final function baz() {}
}',
            ],
            'static-then-visibility-then-final' => [
                '<?php
final class SomeClass {
    static public function foo() {}
    static protected function bar() {}
    static private function baz() {}
}',
                '<?php
final class SomeClass {
    static public final function foo() {}
    static protected final function bar() {}
    static private final function baz() {}
}',
            ],
            'static-then-final-then-visibility' => [
                '<?php
final class SomeClass {
    static public function foo() {}
    static protected function bar() {}
    static private function baz() {}
}',
                '<?php
final class SomeClass {
    static final public function foo() {}
    static final protected function bar() {}
    static final private function baz() {}
}',
            ],
            'no-visibility' => [
                '<?php
final class Foo {
    function foo() {}
    function bar() {}
    function baz() {}
}',
                '<?php
final class Foo {
    final function foo() {}
    final function bar() {}
    final function baz() {}
}',
            ],
            'no-visibility-final-then-static' => [
                '<?php
final class SomeClass {
    static function foo() {}
    static function bar() {}
    static function baz() {}
}',
                '<?php
final class SomeClass {
    final static function foo() {}
    final static function bar() {}
    final static function baz() {}
}',
            ],
            'no-visibility-static-then-final' => [
                '<?php
final class SomeClass {
    static function foo() {}
    static function bar() {}
    static function baz() {}
}',
                '<?php
final class SomeClass {
    static final function foo() {}
    static final function bar() {}
    static final function baz() {}
}',
            ],
            'preserve-comment' => [
                '<?php final class Foo { /* comment */public function foo() {} }',
                '<?php final class Foo { final/* comment */public function foo() {} }',
            ],
            'multiple-classes-per-file' => [
                '<?php final class Foo { public function foo() {} } abstract class Bar { final public function bar() {} }',
                '<?php final class Foo { final public function foo() {} } abstract class Bar { final public function bar() {} }',
            ],
            'non-final' => [
                '<php class Foo { final public function foo() {} }',
            ],
            'abstract-class' => [
                '<php abstract class Foo { final public function foo() {} }',
            ],
            'trait' => [
                '<php trait Foo { final public function foo() {} }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     * @dataProvider providePhp70Cases
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePhp70Cases()
    {
        return [
            'anonymous-class-inside' => [
                '<?php
final class Foo
{
    public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
                '<?php
final class Foo
{
    final public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
            ],
        ];
    }
}
