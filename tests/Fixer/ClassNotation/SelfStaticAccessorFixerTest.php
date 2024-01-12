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

    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            <<<'EOD'
                <?php
                final class Sample
                {
                    public function getBar()
                    {
                        return self::class.self::test();
                    }

                    private static function test()
                    {
                        return 'test';
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                final class Sample
                {
                    public function getBar()
                    {
                        return static::class.static::test();
                    }

                    private static function test()
                    {
                        return 'test';
                    }
                }

                EOD,
        ];

        yield 'multiple' => [
            <<<'EOD'
                <?php
                                    final class Foo0 { public function A(){ return self::A; }}
                                    final class Foo1 { public function A(){ return self::A; }}
                                    final class Foo2 { public function A(){ return self::A; }}
                                    final class Foo3 { public function A(){ return self::A; }}
                                    final class Foo4{public function A(){return self::A;}}final class Foo5{public function A(){return self::A;}}
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    final class Foo0 { public function A(){ return static::A; }}
                                    final class Foo1 { public function A(){ return static::A; }}
                                    final class Foo2 { public function A(){ return static::A; }}
                                    final class Foo3 { public function A(){ return static::A; }}
                                    final class Foo4{public function A(){return static::A;}}final class Foo5{public function A(){return static::A;}}
                EOD."\n                ",
        ];

        yield 'comments and casing' => [
            <<<'EOD'
                <?php
                FINAL CLASS Sample
                {
                    public function getBar()
                    {
                        return/* 0 */self/* 1 */::/* 2 */CLASS/* 3 */;
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                FINAL CLASS Sample
                {
                    public function getBar()
                    {
                        return/* 0 */STATIC/* 1 */::/* 2 */CLASS/* 3 */;
                    }
                }

                EOD,
        ];

        yield 'not final' => [
            <<<'EOD'
                <?php
                class Sample
                {
                    public function getBar()
                    {
                        return static::class;
                    }
                }

                EOD,
        ];

        yield 'abstract' => [
            <<<'EOD'
                <?php
                abstract class Sample
                {
                    public function getBar()
                    {
                        return static::class;
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                final class Foo
                {
                    public function bar()
                    {
                        $a = new Foo();

                        return new self();
                    }
                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                final class Foo
                {
                    public function bar()
                    {
                        $a = new Foo();

                        return new static();
                    }
                }
                EOD."\n                ",
        ];

        yield 'instance of' => [
            <<<'EOD'
                <?php
                final class Foo
                {
                    public function isBar($foo)
                    {
                        return $foo instanceof self;
                    }
                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                final class Foo
                {
                    public function isBar($foo)
                    {
                        return $foo instanceof static;
                    }
                }
                EOD."\n                ",
        ];

        yield 'in method as new' => [
            '<?php final class A { public static function b() { return new self(); } }',
            '<?php final class A { public static function b() { return new static(); } }',
        ];

        yield 'in method as new with comments' => [
            '<?php final class A { public static function b() { return new /* hmm */ self(); } }',
            '<?php final class A { public static function b() { return new /* hmm */ static(); } }',
        ];

        yield 'in method as new without parentheses' => [
            '<?php final class A { public static function b() { return new self; } }',
            '<?php final class A { public static function b() { return new static; } }',
        ];

        yield 'simple anonymous class' => [
            <<<'EOD'
                <?php
                $a = new class {
                    public function getBar()
                    {
                        return self::class;
                    }
                };
                EOD,
            <<<'EOD'
                <?php
                $a = new class {
                    public function getBar()
                    {
                        return static::class;
                    }
                };
                EOD,
        ];

        yield 'nested anonymous class' => [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];

        yield 'anonymous classes inside lambda' => [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];

        yield 'no scope' => [
            '<?php echo static::class;',
        ];

        yield 'do not fix inside lambda' => [
            <<<'EOD'
                <?php
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

                EOD,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'enums' => [
            <<<'EOD'
                <?php
                enum Foo
                {
                    case Baz;

                    private const BAR = 'foo';

                    public static function bar(): Foo
                    {
                        return self::Baz;
                    }

                    public static function baz(mixed $other): void
                    {
                        if ($other instanceof self) {
                            echo self::BAR;
                        }
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                enum Foo
                {
                    case Baz;

                    private const BAR = 'foo';

                    public static function bar(): Foo
                    {
                        return static::Baz;
                    }

                    public static function baz(mixed $other): void
                    {
                        if ($other instanceof static) {
                            echo static::BAR;
                        }
                    }
                }

                EOD,
        ];

        yield 'enum with nested anonymous class' => [
            <<<'EOD'
                <?php
                                enum Suit: int implements SomeIntInterface, Z
                                {
                                    case Hearts = 1;
                                    case Clubs = 3;
                                    public const HEARTS = self::Hearts;

                                    public function Foo(): string
                                    {
                                        return self::Hearts->Bar()->getBar() . self::class . self::Clubs->value;
                                    }

                                    public function Bar(): object
                                    {
                                        return new class {
                                            public function getBar()
                                            {
                                                return self::class;
                                            }
                                        };
                                    }
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                enum Suit: int implements SomeIntInterface, Z
                                {
                                    case Hearts = 1;
                                    case Clubs = 3;
                                    public const HEARTS = self::Hearts;

                                    public function Foo(): string
                                    {
                                        return static::Hearts->Bar()->getBar() . static::class . static::Clubs->value;
                                    }

                                    public function Bar(): object
                                    {
                                        return new class {
                                            public function getBar()
                                            {
                                                return static::class;
                                            }
                                        };
                                    }
                                }
                EOD."\n            ",
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
            <<<'EOD'
                <?php
                final readonly class Sample
                {
                    public function getBar()
                    {
                        return self::class.self::test();
                    }

                    private static function test()
                    {
                        return 'test';
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                final readonly class Sample
                {
                    public function getBar()
                    {
                        return static::class.static::test();
                    }

                    private static function test()
                    {
                        return 'test';
                    }
                }

                EOD,
        ];
    }
}
