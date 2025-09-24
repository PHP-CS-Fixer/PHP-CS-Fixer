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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\StaticPrivateMethodFixer>
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\StaticPrivateMethodFixer
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class StaticPrivateMethodFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'call from other method' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = $this->baz;
                        $var = self::baz();
                        if (true) {
                            $var = self::baz();
                        }
                    }

                    private static function baz()
                    {
                        if (true) {
                            return 1;
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = $this->baz;
                        $var = $this->baz();
                        if (true) {
                            $var = $this->baz();
                        }
                    }

                    private function baz()
                    {
                        if (true) {
                            return 1;
                        }
                    }
                }
                PHP,
        ];

        yield 'multiple classes' => [
            <<<'PHP'
                <?php
                abstract class Foo
                {
                    private static function baz() { return 1; }

                    public function xyz()
                    {
                        return 1;
                    }
                }
                abstract class Bar
                {
                    public function baz() { return 1; }

                    abstract protected function xyz1();
                    protected abstract function xyz2();
                    abstract function xyz3();
                }
                PHP,
            <<<'PHP'
                <?php
                abstract class Foo
                {
                    private function baz() { return 1; }

                    public function xyz()
                    {
                        return 1;
                    }
                }
                abstract class Bar
                {
                    public function baz() { return 1; }

                    abstract protected function xyz1();
                    protected abstract function xyz2();
                    abstract function xyz3();
                }
                PHP,
        ];

        yield 'already static' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private static function foo1() { return 1; }
                    static private function foo2() { return 1; }
                }
                PHP,
        ];

        yield 'methods containing closures' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private function bar()
                    {
                        return function() {};
                    }

                    private function baz()
                    {
                        return static function() {};
                    }
                }
                PHP,
        ];

        yield 'instance reference' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private function bar()
                    {
                        if (true) {
                            return $this;
                        }
                    }
                }
                PHP,
        ];

        yield 'debug_backtrace' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private function bar()
                    {
                        return debug_backtrace()[1]['object'];
                    }
                }
                PHP,
        ];

        yield 'references inside non static closures' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = function() {
                            $var = $this->baz;
                            $var = self::baz();
                        };
                        // Non valid in runtime, but valid syntax
                        $var = static function() {
                            $var = $this->baz();
                        };
                    }

                    private static function baz()
                    {
                        return 1;
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = function() {
                            $var = $this->baz;
                            $var = $this->baz();
                        };
                        // Non valid in runtime, but valid syntax
                        $var = static function() {
                            $var = $this->baz();
                        };
                    }

                    private function baz()
                    {
                        return 1;
                    }
                }
                PHP,
        ];

        yield 'magic methods' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private function __clone() {}
                    private function __construct() {}
                    private function __destruct() {}
                    private function __wakeup() {}
                }
                PHP,
        ];

        yield 'multiple methods' => [
            self::generate50Samples(true),
            self::generate50Samples(false),
        ];

        yield 'method calling itself' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    private static function baz()
                    {
                        return self::baz();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    private function baz()
                    {
                        return $this->baz();
                    }
                }
                PHP,
        ];

        yield 'trait' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    use A, B, C {
                        asd as lol;
                    }

                    private static function bar() {}
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    use A, B, C {
                        asd as lol;
                    }

                    private function bar() {}
                }
                PHP,
        ];

        yield 'final' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    final private static function baz1() { return 1; }
                    private final static function baz2() { return 1; }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    final private function baz1() { return 1; }
                    private final function baz2() { return 1; }
                }
                PHP,
        ];

        yield 'call from other method with anonymous class' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = $this->baz;
                        $var = self::baz();
                        if (true) {
                            $var = self::baz();
                        }
                    }

                    private static function baz()
                    {
                        if (true) {
                            return new class() {
                                public function baz()
                                {
                                    return $this;
                                }
                            };
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = $this->baz;
                        $var = $this->baz();
                        if (true) {
                            $var = $this->baz();
                        }
                    }

                    private function baz()
                    {
                        if (true) {
                            return new class() {
                                public function baz()
                                {
                                    return $this;
                                }
                            };
                        }
                    }
                }
                PHP,
        ];

        yield 'multiple classes with anonymous class' => [
            <<<'PHP'
                <?php
                abstract class Foo
                {
                    private static function baz() { return 1; }

                    public function xyz()
                    {
                        return new class() extends Wut {
                            public function anonym_xyz()
                            {
                                return $this->baz();
                            }
                        };
                    }
                }
                abstract class Bar
                {
                    public function baz() { return 1; }

                    abstract protected function xyz1();
                    protected abstract function xyz2();
                    abstract function xyz3();
                }
                PHP,
            <<<'PHP'
                <?php
                abstract class Foo
                {
                    private function baz() { return 1; }

                    public function xyz()
                    {
                        return new class() extends Wut {
                            public function anonym_xyz()
                            {
                                return $this->baz();
                            }
                        };
                    }
                }
                abstract class Bar
                {
                    public function baz() { return 1; }

                    abstract protected function xyz1();
                    protected abstract function xyz2();
                    abstract function xyz3();
                }
                PHP,
        ];

        yield 'references inside non-static closures with anonymous class' => [
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = function() {
                            $var = $this->baz;
                            $var = self::baz();
                            $var = new class() {
                                public function foo()
                                {
                                    return $this->baz();
                                }
                            };
                        };
                        // Non valid in runtime, but valid syntax
                        $var = static function() {
                            $var = $this->baz();
                        };
                    }

                    private static function baz()
                    {
                        return 1;
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo
                {
                    public $baz;

                    public function bar()
                    {
                        $var = function() {
                            $var = $this->baz;
                            $var = $this->baz();
                            $var = new class() {
                                public function foo()
                                {
                                    return $this->baz();
                                }
                            };
                        };
                        // Non valid in runtime, but valid syntax
                        $var = static function() {
                            $var = $this->baz();
                        };
                    }

                    private function baz()
                    {
                        return 1;
                    }
                }
                PHP,
        ];

        yield 'nested calls' => [
            <<<'PHP'
                <?php class Foo {
                    public function the_function() { return self::function_0(); }
                    private static function function_2() { return self::function_3(); }
                    private static function function_1() { return self::function_2(); }
                    private static function function_3() { return self::function_4(); }
                    private static function function_7() { return self::function_8(); }
                    private static function function_9() { return null; }
                    private static function function_5() { return self::function_6(); }
                    private static function function_8() { return self::function_9(); }
                    private static function function_0() { return self::function_1(); }
                    private static function function_4() { return self::function_5(); }
                    private static function function_6() { return self::function_7(); }
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    public function the_function() { return $this->function_0(); }
                    private function function_2() { return $this->function_3(); }
                    private function function_1() { return $this->function_2(); }
                    private function function_3() { return $this->function_4(); }
                    private function function_7() { return $this->function_8(); }
                    private function function_9() { return null; }
                    private function function_5() { return $this->function_6(); }
                    private function function_8() { return $this->function_9(); }
                    private function function_0() { return $this->function_1(); }
                    private function function_4() { return $this->function_5(); }
                    private function function_6() { return $this->function_7(); }
                }
                PHP,
        ];
    }

    private static function generate50Samples(bool $fixed): string
    {
        $template = <<<'PHP'
            <?php
            class Foo
            {
                public function userMethodStart()
                {
            %s
                }
            %s
            }

            PHP;
        $usage = '';
        $signature = '';
        for ($inc = 0; $inc < 50; ++$inc) {
            $usage .= \sprintf('$var = %sbar%02s();%s', $fixed ? 'self::' : '$this->', $inc, \PHP_EOL);
            $signature .= \sprintf('private %sfunction bar%02s() {}%s', $fixed ? 'static ' : '', $inc, \PHP_EOL);
        }

        return \sprintf($template, $usage, $signature);
    }
}
