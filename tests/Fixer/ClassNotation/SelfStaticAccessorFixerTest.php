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
 * @covers \PhpCsFixer\Fixer\ClassNotation\SelfStaticAccessorFixer
 */
final class SelfStaticAccessorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'simple' => [
                '<?php
final class Sample
{
    public function getBar()
    {
        return self::class.self::test();
    }

    private static function test()
    {
        return \'test\';
    }
}
',
                '<?php
final class Sample
{
    public function getBar()
    {
        return static::class.static::test();
    }

    private static function test()
    {
        return \'test\';
    }
}
',
            ],
            'multiple' => [
                '<?php
                    final class Foo0 { public function A(){ return self::A; }}
                    final class Foo1 { public function A(){ return self::A; }}
                    final class Foo2 { public function A(){ return self::A; }}
                    final class Foo3 { public function A(){ return self::A; }}
                    final class Foo4{public function A(){return self::A;}}final class Foo5{public function A(){return self::A;}}
                ',
                '<?php
                    final class Foo0 { public function A(){ return static::A; }}
                    final class Foo1 { public function A(){ return static::A; }}
                    final class Foo2 { public function A(){ return static::A; }}
                    final class Foo3 { public function A(){ return static::A; }}
                    final class Foo4{public function A(){return static::A;}}final class Foo5{public function A(){return static::A;}}
                ',
            ],
            'comments and casing' => [
                '<?php
FINAL CLASS Sample
{
    public function getBar()
    {
        return/* 0 */self/* 1 */::/* 2 */CLASS/* 3 */;
    }
}
',
                '<?php
FINAL CLASS Sample
{
    public function getBar()
    {
        return/* 0 */STATIC/* 1 */::/* 2 */CLASS/* 3 */;
    }
}
',
            ],
            'not final' => [
                '<?php
class Sample
{
    public function getBar()
    {
        return static::class;
    }
}
',
            ],
            'abstract' => [
                '<?php
abstract class Sample
{
    public function getBar()
    {
        return static::class;
    }
}
',
            ],
            [
                '<?php
final class Foo
{
    public function bar()
    {
        $a = new Foo();

        return new self();
    }
}
                ',
                '<?php
final class Foo
{
    public function bar()
    {
        $a = new Foo();

        return new static();
    }
}
                ',
            ],
            'instance of' => [
                '<?php
final class Foo
{
    public function isBar($foo)
    {
        return $foo instanceof self;
    }
}
                ',
                '<?php
final class Foo
{
    public function isBar($foo)
    {
        return $foo instanceof static;
    }
}
                ',
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
            'simple anonymous class' => [
                '<?php
$a = new class {
    public function getBar()
    {
        return self::class;
    }
};',
                '<?php
$a = new class {
    public function getBar()
    {
        return static::class;
    }
};',
            ],
            'nested anonymous class' => [
                '<?php
final class Foo
{
    public function Foo()
    {
        return self::class;
    }

    public function getClass()
    {
        $a = new class() {
            public function getBar()
            {
                return self::class;
            }
        };
    }

    public function Foo2()
    {
        return self::class;
    }
}
',
                '<?php
final class Foo
{
    public function Foo()
    {
        return static::class;
    }

    public function getClass()
    {
        $a = new class() {
            public function getBar()
            {
                return static::class;
            }
        };
    }

    public function Foo2()
    {
        return static::class;
    }
}
',
            ],
            'anonymous classes inside lambda' => [
                '<?php
final class Foo
{
    public function bar()
    {
        echo self::class; // do fix

        return function () {
            echo static::class; // do not fix

            $a = new class {
                public function bar2()
                {
                    echo self::class; // do fix

                    return function () {
                        echo static::class; // do not fix

                        $a = new class {
                            public function bar2()
                            {
                                echo self::class; // do fix
                            }
                        };
                        echo static::class; // do not fix

                        return $a;
                    };
                }
            };
            echo static::class; // do not fix

            $b = new class {
                public function test()
                {
                    echo self::class; // do fix
                }
            };

            return $a;
        };

        echo self::class; // do fix
    }
}
',
                '<?php
final class Foo
{
    public function bar()
    {
        echo static::class; // do fix

        return function () {
            echo static::class; // do not fix

            $a = new class {
                public function bar2()
                {
                    echo static::class; // do fix

                    return function () {
                        echo static::class; // do not fix

                        $a = new class {
                            public function bar2()
                            {
                                echo static::class; // do fix
                            }
                        };
                        echo static::class; // do not fix

                        return $a;
                    };
                }
            };
            echo static::class; // do not fix

            $b = new class {
                public function test()
                {
                    echo static::class; // do fix
                }
            };

            return $a;
        };

        echo static::class; // do fix
    }
}
',
            ],
            'no scope' => [
                '<?php echo static::class;',
            ],
            'do not fix inside lambda' => [
                '<?php
final class Foo
{
    public function Bar()
    {
        return function() {
            return static::class;
        };
    }
}

$a = static function() { return static::class; };
$b = function() { return static::class; };
',
            ],
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'simple' => [
            '<?php
final readonly class Sample
{
    public function getBar()
    {
        return self::class.self::test();
    }

    private static function test()
    {
        return \'test\';
    }
}
',
            '<?php
final readonly class Sample
{
    public function getBar()
    {
        return static::class.static::test();
    }

    private static function test()
    {
        return \'test\';
    }
}
',
        ];
    }
}
