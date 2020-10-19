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
            'in method as new' => [
                '<?php final class A { public static function b() { return new self(); } }',
                '<?php final class A { public static function b() { return new static(); } }',
            ],
            'in method as new with comments' => [
                '<?php final class A { public static function b() { return new /* hmm */ self(); } }',
                '<?php final class A { public static function b() { return new /* hmm */ static(); } }',
            ],
            'in method as new without parentheses' => [
                '<?php final class A { public static function b() { return new self; } }',
                '<?php final class A { public static function b() { return new static; } }',
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
}
