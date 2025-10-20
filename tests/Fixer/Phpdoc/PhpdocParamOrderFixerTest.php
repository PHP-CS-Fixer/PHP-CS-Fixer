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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer>
 *
 * @author Jonathan Gruber <gruberjonathan@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpdocParamOrderFixerTest extends AbstractFixerTestCase
{
    /**
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
        yield 'no changes' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param $a
                 */
                public function m($a) {}
            }
            EOT];

        yield 'no changes multiline' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param string $a
                 * @param bool   $b
                 */
                public function m($a, $b) {}
            }
            EOT];

        yield 'only params untyped' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $b
                     * @param $c
                     * @param $d
                     * @param $e
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $b
                     * @param $e
                     * @param $a
                     * @param $c
                     * @param $d
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'only params untyped mixed' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param int $a
                     * @param $b
                     * @param $c
                     * @param bool $d
                     * @param $e
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $c
                     * @param $e
                     * @param int $a
                     * @param $b
                     * @param bool $d
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'only params typed' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param string $a
                     * @param bool   $b
                     * @param string $c
                     * @param string $d
                     * @param int $e
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param bool   $b
                     * @param string $a
                     * @param string $c
                     * @param int $e
                     * @param string $d
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'only params undocumented' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $b
                     * @param $c
                     * @param $d
                     */
                    public function m($a, $b, $c, $d, $e, $f) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $c
                     * @param $d
                     * @param $b
                     */
                    public function m($a, $b, $c, $d, $e, $f) {}
                }
                EOT,
        ];

        yield 'only params superfluous annotation' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param $a
                 * @param $b
                 * @param $c
                 * @param $superfluous
                 */
                public function m($a, $b, $c) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $superfluous
                     * @param $b
                     * @param $c
                     */
                    public function m($a, $b, $c) {}
                }
                EOT,
        ];

        yield 'only params superfluous annotations' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $b
                     * @param $c
                     * @param $superfluous2
                     * @param $superfluous1
                     * @param $superfluous3
                     */
                    public function m($a, $b, $c) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $a
                     * @param $superfluous2
                     * @param $b
                     * @param $superfluous1
                     * @param $c
                     * @param $superfluous3
                     */
                    public function m($a, $b, $c) {}
                }
                EOT,
        ];

        yield 'params untyped' => [<<<'EOT'
            <?php
            class C {
                /**
                 * Some function
                 *
                 * @param $a
                 * @param $b
                 * @param $c
                 *
                 * @throws \Exception
                 *
                 * @return bool
                 */
                public function m($a, $b, $c, $d, $e) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param $b
                     * @param $c
                     * @param $a
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'params typed' => [
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param Foo $a
                     * @param int $b
                     * @param bool $c
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param int $b
                     * @param bool $c
                     * @param Foo $a
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'params description' => [<<<'EOT'
            <?php
            class C {
                /**
                 * Some function
                 *
                 * @param Foo $a A parameter
                 * @param int $b B parameter
                 * @param bool $c C parameter
                 *
                 * @throws \Exception
                 *
                 * @return bool
                 */
                public function m($a, $b, $c, $d, $e) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param int $b B parameter
                     * @param bool $c C parameter
                     * @param Foo $a A parameter
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'params multiline description' => [<<<'EOT'
            <?php
            class C {
                /**
                 * Some function
                 *
                 * @param Foo $a A parameter
                 * @param int $b B parameter
                 * @param bool $c Another multiline, longer
                 *                description of C parameter
                 * @param bool $d Multiline description
                 *                of D parameter
                 *
                 * @throws \Exception
                 *
                 * @return bool
                 */
                public function m($a, $b, $c, $d) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param int $b B parameter
                     * @param bool $d Multiline description
                     *                of D parameter
                     * @param bool $c Another multiline, longer
                     *                description of C parameter
                     * @param Foo $a A parameter
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d) {}
                }
                EOT,
        ];

        yield 'complex types' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param Foo[]|\Bar\Baz $a
                 * @param Foo|Bar $b
                 * @param array<int, FooInterface>|string $c
                 * @param array<array{int, int}> $d
                 * @param ?Foo $e
                 * @param \Closure(( $b is Bar ? bool : int)): $this $f
                 */
                public function m($a, $b, $c, $d, $e, $f) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param array<int, FooInterface>|string $c
                     * @param Foo|Bar $b
                     * @param array<array{int, int}> $d
                     * @param ?Foo $e
                     * @param \Closure(( $b is Bar ? bool : int)): $this $f
                     * @param Foo[]|\Bar\Baz $a
                     */
                    public function m($a, $b, $c, $d, $e, $f) {}
                }
                EOT,
        ];

        yield 'various method declarations' => [<<<'EOT'
            <?php
            abstract class C {
                /**
                 * @param Foo   $a
                 * @param array $b
                 * @param       $c
                 * @param mixed $d
                 */
                final public static function m1(Foo $a, array $b, $c, $d) {}

                /**
                 * @param array $a
                 * @param       $b
                 * @throws Exception
                 *
                 * @return bool
                 */
                abstract public function m2(array $a, $b);

                /**
                 * Description of
                 * method
                 *
                 * @param int    $a
                 * @param Foo    $b
                 * @param        $c
                 * @param Bar    $d
                 * @param string $e
                 */
                protected static function m3($a, Foo $b, $c, Bar $d, $e) {}

                /**
                 * @see Something
                 *
                 * @param callable $a
                 * @param          $b
                 * @param array    $c
                 * @param array    $d
                 *
                 * @return int
                 *
                 * Text
                 */
                final protected function m4(Callable $a, $b, array $c, array $d) {}

                /**
                 * @param Bar   $a
                 * @param Bar   $b
                 * @param       $c
                 * @param int   $d
                 * @param array $e
                 * @param       $f
                 *
                 * @return Foo|null
                 */
                abstract protected function m5(Bar $a, Bar $b, $c, $d, array $e, $f);

                /**
                 * @param array $a
                 * @param       $b
                 */
                private function m6(array $a, $b) {}

                /**
                 * @param Foo   $a
                 * @param array $b
                 * @param mixed $c
                 */
                private static function m7(Foo $a, array $b, $c) {}
            }
            EOT,
            <<<'EOT'
                <?php
                abstract class C {
                    /**
                     * @param array $b
                     * @param Foo   $a
                     * @param mixed $d
                     * @param       $c
                     */
                    final public static function m1(Foo $a, array $b, $c, $d) {}

                    /**
                     * @param       $b
                     * @param array $a
                     * @throws Exception
                     *
                     * @return bool
                     */
                    abstract public function m2(array $a, $b);

                    /**
                     * Description of
                     * method
                     *
                     * @param string $e
                     * @param int    $a
                     * @param Foo    $b
                     * @param Bar    $d
                     * @param        $c
                     */
                    protected static function m3($a, Foo $b, $c, Bar $d, $e) {}

                    /**
                     * @see Something
                     *
                     * @param          $b
                     * @param array    $d
                     * @param array    $c
                     * @param callable $a
                     *
                     * @return int
                     *
                     * Text
                     */
                    final protected function m4(Callable $a, $b, array $c, array $d) {}

                    /**
                     * @param Bar   $b
                     * @param       $f
                     * @param int   $d
                     * @param array $e
                     * @param       $c
                     * @param Bar   $a
                     *
                     * @return Foo|null
                     */
                    abstract protected function m5(Bar $a, Bar $b, $c, $d, array $e, $f);

                    /**
                     * @param       $b
                     * @param array $a
                     */
                    private function m6(array $a, $b) {}

                    /**
                     * @param array $b
                     * @param mixed $c
                     * @param Foo   $a
                     */
                    private static function m7(Foo $a, array $b, $c) {}
                }
                EOT,
        ];

        yield 'params with other annotations in between' => [<<<'EOT'
            <?php
            /**
             * [c1] Method description
             * [c2] over multiple lines
             *
             * @see Baz
             *
             * @param int   $a Long param
             *                 description
             * @param mixed $b
             * @param mixed $superfluous1 With text
             * @param int $superfluous2
             * @return array Long return
             *               description
             * @throws Exception
             * @throws FooException
             */
            function foo($a, $b) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * [c1] Method description
                 * [c2] over multiple lines
                 *
                 * @see Baz
                 *
                 * @param mixed $b
                 * @param mixed $superfluous1 With text
                 * @return array Long return
                 *               description
                 * @param int $superfluous2
                 * @throws Exception
                 * @param int   $a Long param
                 *                 description
                 * @throws FooException
                 */
                function foo($a, $b) {}
                EOT,
        ];

        yield 'params blank lines' => [<<<'EOT'
            <?php
            class C {
                /**
                 * Some function
                 *
                 * @param $a
                 * @param $b
                 *
                 * @param $c
                 *
                 *
                 * @param $d
                 *
                 * @param $e
                 *
                 * @throws \Exception
                 *
                 * @return bool
                 */
                public function m($a, $b, $c, $d, $e) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * Some function
                     *
                     * @param $b
                     * @param $e
                     *
                     * @param $c
                     *
                     *
                     * @param $a
                     *
                     * @param $d
                     *
                     * @throws \Exception
                     *
                     * @return bool
                     */
                    public function m($a, $b, $c, $d, $e) {}
                }
                EOT,
        ];

        yield 'nested phpdoc' => [<<<'EOT'
            <?php
            /**
             * @param string[] $array
             * @param callable $callback {
             *     @param string $value
             *     @param int    $key
             *     @param mixed  $userdata
             * }
             * @param mixed    $userdata
             *
             * @return bool
             */
            function string_array_walk(array &$array, callable $callback, $userdata = null) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param callable $callback {
                 *     @param string $value
                 *     @param int    $key
                 *     @param mixed  $userdata
                 * }
                 * @param mixed    $userdata
                 * @param string[] $array
                 *
                 * @return bool
                 */
                function string_array_walk(array &$array, callable $callback, $userdata = null) {}
                EOT,
        ];

        yield 'multi nested phpdoc' => [<<<'EOT'
            <?php
            /**
             * @param string[] $a
             * @param callable $b {
             *     @param string   $a
             *     @param callable {
             *         @param string   $d
             *         @param int      $a
             *         @param callable $c {
             *             $param string $e
             *         }
             *     }
             *     @param mixed    $b2
             * }
             * @param mixed    $c
             * @param int      $d
             *
             * @return bool
             */
            function m(array &$a, callable $b, $c = null, $d) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param mixed    $c
                 * @param callable $b {
                 *     @param string   $a
                 *     @param callable {
                 *         @param string   $d
                 *         @param int      $a
                 *         @param callable $c {
                 *             $param string $e
                 *         }
                 *     }
                 *     @param mixed    $b2
                 * }
                 * @param int      $d
                 * @param string[] $a
                 *
                 * @return bool
                 */
                function m(array &$a, callable $b, $c = null, $d) {}
                EOT,
        ];

        yield 'multiple nested phpdoc' => [<<<'EOT'
            <?php
            /**
             * @param string[] $array
             * @param callable $callback {
             *     @param string $value
             *     @param int    $key
             *     @param mixed  $userdata {
             *         $param array $array
             *     }
             * }
             * @param mixed    $userdata
             * @param callable $foo {
             *     @param callable {
             *         @param string $inner1
             *         @param int    $inner2
             *     }
             *     @param mixed  $userdata
             * }
             * @param $superfluous1 Superfluous
             * @param $superfluous2 Superfluous
             *
             * @return bool
             */
            function string_array_walk(array &$array, callable $callback, $userdata = null, $foo) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param $superfluous1 Superfluous
                 * @param callable $callback {
                 *     @param string $value
                 *     @param int    $key
                 *     @param mixed  $userdata {
                 *         $param array $array
                 *     }
                 * }
                 * @param $superfluous2 Superfluous
                 * @param callable $foo {
                 *     @param callable {
                 *         @param string $inner1
                 *         @param int    $inner2
                 *     }
                 *     @param mixed  $userdata
                 * }
                 * @param mixed    $userdata
                 * @param string[] $array
                 *
                 * @return bool
                 */
                function string_array_walk(array &$array, callable $callback, $userdata = null, $foo) {}
                EOT,
        ];

        yield 'non-matching param name' => [<<<'EOT'
            <?php
            /**
             * @param Foo $fooBar
             * @param $fooSomethingNotMatchingTheName
             * @param OtherClassLorem $x
             */
            function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param $fooSomethingNotMatchingTheName
                 * @param Foo $fooBar
                 * @param OtherClassLorem $x
                 */
                function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
                EOT,
        ];

        yield 'plain function' => [<<<'EOT'
            <?php
            /**
             * A plain function
             *
             * @param $a
             * @param $b
             * @param $c
             * @param $d
             */
            function m($a, $b, $c, $d) {}
            EOT,
            <<<'EOT'
                <?php
                /**
                 * A plain function
                 *
                 * @param $c
                 * @param $b
                 * @param $d
                 * @param $a
                 */
                function m($a, $b, $c, $d) {}
                EOT,
        ];

        yield 'comments in signature' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param $a
                 * @param $b
                 * @param $c
                 * @param $d
                 */
                public/*1*/function/*2*/m/*3*/(/*4*/$a, $b,/*5*/$c, $d){}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $d
                     * @param $a
                     * @param $b
                     * @param $c
                     */
                    public/*1*/function/*2*/m/*3*/(/*4*/$a, $b,/*5*/$c, $d){}
                }
                EOT,
        ];

        yield 'closure' => [<<<'EOT'
            <?php
            /**
             * @param array $a
             * @param       $b
             * @param Foo   $c
             * @param int   $d
             */
            $closure = function (array $a, $b, Foo $c, $d) {};
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param       $b
                 * @param int   $d
                 * @param Foo   $c
                 * @param array $a
                 */
                $closure = function (array $a, $b, Foo $c, $d) {};
                EOT,
        ];

        yield 'arrow function' => [<<<'EOT'
            <?php
            /**
             * @param array $a
             * @param       $b
             * @param Foo   $c
             * @param int   $d
             */
            $closure = fn (array $a, $b, Foo $c, $d) => null;
            EOT,
            <<<'EOT'
                <?php
                /**
                 * @param       $b
                 * @param int   $d
                 * @param Foo   $c
                 * @param array $a
                 */
                $closure = fn (array $a, $b, Foo $c, $d) => null;
                EOT,
        ];

        yield 'interface' => [<<<'EOT'
            <?php
            Interface I
            {
                /**
                 * @param string $a
                 * @param array  $b
                 * @param Foo    $c
                 *
                 * @return int|null
                 */
                public function foo($a, array $b, Foo $c);

                /**
                 * @param array $a
                 * @param       $b
                 *
                 * @return bool
                 */
                public static function bar(array $a, $b);
            }
            EOT,
            <<<'EOT'
                <?php
                Interface I
                {
                    /**
                     * @param Foo    $c
                     * @param string $a
                     * @param array  $b
                     *
                     * @return int|null
                     */
                    public function foo($a, array $b, Foo $c);

                    /**
                     * @param       $b
                     * @param array $a
                     *
                     * @return bool
                     */
                    public static function bar(array $a, $b);
                }
                EOT,
        ];

        yield 'PHP 7 param types' => [<<<'EOT'
            <?php
            class C {
                /**
                 * @param array $a
                 * @param $b
                 * @param bool $c
                 */
                public function m(array $a, $b, bool $c) {}
            }
            EOT,
            <<<'EOT'
                <?php
                class C {
                    /**
                     * @param $b
                     * @param bool $c
                     * @param array $a
                     */
                    public function m(array $a, $b, bool $c) {}
                }
                EOT,
        ];
    }

    /**
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $configuration, string $expectedMessage): void
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure($configuration);
    }

    /**
     * @return iterable<string, array{configuration: array<string, mixed>, expectedMessage: string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'invalid alias format with @' => [
            'configuration' => ['param_aliases' => ['invalid@tag']],
            'expectedMessage' => '/invalid tag/',
        ];

        yield 'non-param alias (return tag)' => [
            'configuration' => ['param_aliases' => ['return']],
            'expectedMessage' => '/invalid tag/',
        ];

        yield 'alias starting with number' => [
            'configuration' => ['param_aliases' => ['123-param']],
            'expectedMessage' => '/invalid tag/',
        ];

        yield 'non-array value' => [
            'configuration' => ['param_aliases' => 'psalm-param'],
            'expectedMessage' => '/expected to be of type "string\[\]"/',
        ];

        yield 'non-string value in array' => [
            'configuration' => ['param_aliases' => ['psalm-param', 123]],
            'expectedMessage' => '/expected to be of type "string\[\]"/',
        ];
    }
}
