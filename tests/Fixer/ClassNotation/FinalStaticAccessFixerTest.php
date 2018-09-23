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
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalStaticAccessFixer
 */
final class FinalStaticAccessFixerTest extends AbstractFixerTestCase
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
            'in method as class' => [
                '<?php final class A { public function b() { echo self::class; } }',
                '<?php final class A { public function b() { echo static::class; } }',
            ],
            'handles alternate case' => [
                '<?php final class A { public function b() { echo self::class; } }',
                '<?php final class A { public function b() { echo sTaTiC::class; } }',
            ],
            'in method as property' => [
                '<?php final class A { public function b() { echo self::$c; } }',
                '<?php final class A { public function b() { echo static::$c; } }',
            ],
            'in method as call' => [
                '<?php final class A { public function b() { echo self::c(); } }',
                '<?php final class A { public function b() { echo static::c(); } }',
            ],
            'in method as const' => [
                '<?php final class A { public function b() { echo self::C; } }',
                '<?php final class A { public function b() { echo static::C; } }',
            ],
            'does not change non-final classes' => [
                '<?php class A { public function b() { echo static::c(); } }',
            ],
            'does not change static property' => [
                '<?php final class A { public static $b = null; }',
            ],
            'does not change static method' => [
                '<?php final class A { public static function b() {} }',
            ],
            'does not change static variables' => [
                '<?php final class A { public function b() { static $c = null; } }',
            ],
            'does not change static lambda' => [
                '<?php final class A { public function b() { $c = static function () {}; } }',
            ],
            'in multiple methods and classes' => [
                '<?php
                    final class X {
                        public function b() { echo self::class; }
                        public function c() { echo self::class; }
                    }
                    final class Y {
                        public static $var = 2;
                        public function b() { echo self::class; }
                        public function c() { static $a = 0; echo self::class.$a; }
                    }
                    class Foo {
                        public function b() { echo static::class; }
                        public function c() { echo static::class; }
                    }
                    final class Z {
                        public function b() { echo self::class; }
                        public function c() { return static function(){}; }
                    }
                ',
                '<?php
                    final class X {
                        public function b() { echo static::class; }
                        public function c() { echo static::class; }
                    }
                    final class Y {
                        public static $var = 2;
                        public function b() { echo static::class; }
                        public function c() { static $a = 0; echo static::class.$a; }
                    }
                    class Foo {
                        public function b() { echo static::class; }
                        public function c() { echo static::class; }
                    }
                    final class Z {
                        public function b() { echo static::class; }
                        public function c() { return static function(){}; }
                    }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            'property' => [
                '<?php final class A { public $b = self::class; }',
                '<?php final class A { public $b = static::class; }',
            ],
            'does not change non-final classes' => [
                '<?php class A { public $b = static::class; }',
            ],
            'does not change anonymous classes' => [
                '<?php $a = new class { public $b = static::class; };',
            ],
            'handles comments' => [
                '<?php /*a*/final/*b*/class/*c*/A { public $b = /*1*/self/*2*/::/*3*/class; }',
                '<?php /*a*/final/*b*/class/*c*/A { public $b = /*1*/static/*2*/::/*3*/class; }',
            ],
            'property and nested anonymous class' => [
                '<?php final class A { public $b = self::class; public function foo(){ return new class { public $b = static::class; }; }}',
                '<?php final class A { public $b = static::class; public function foo(){ return new class { public $b = static::class; }; }}',
            ],
            'property and nested anonymous class with set function' => [
                '<?php final class A { public $b = self::class; public function foo(){ return new class ($a = function () {}) { public $b = static::class; }; }}',
                '<?php final class A { public $b = static::class; public function foo(){ return new class ($a = function () {}) { public $b = static::class; }; }}',
            ],
            'property and nested anonymous class with set anonymous class' => [
                '<?php final class A { public $b = self::class; public function foo(){ return new class ($a = new class {}) { public $b = static::class; }; }}',
                '<?php final class A { public $b = static::class; public function foo(){ return new class ($a = new class {}) { public $b = static::class; }; }}',
            ],
            'property and nested anonymous class with change after' => [
                '<?php final class A { public function foo(){ return new class { public $b = static::class; }; } public $b = self::class; }',
                '<?php final class A { public function foo(){ return new class { public $b = static::class; }; } public $b = static::class; }',
            ],
            'property and nested anonymous class with extends' => [
                '<?php final class A { public $b = self::class; public function foo(){ return new class extends X implements Y { public $b = static::class; }; }}',
                '<?php final class A { public $b = static::class; public function foo(){ return new class extends X implements Y { public $b = static::class; }; }}',
            ],
        ];
    }
}
