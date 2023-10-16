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
 * @author Jonathan Gruber <gruberjonathan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer
 */
final class PhpdocParamOrderFixerTest extends AbstractFixerTestCase
{
    public function testNoChanges(): void
    {
        $expected = <<<'EOT'
            <?php
            class C {
                /**
                 * @param $a
                 */
                public function m($a) {}
            }
            EOT;
        $this->doTest($expected);
    }

    public function testNoChangesMultiple(): void
    {
        $expected = <<<'EOT'
            <?php
            class C {
                /**
                 * @param string $a
                 * @param bool   $b
                 */
                public function m($a, $b) {}
            }
            EOT;
        $this->doTest($expected);
    }

    public function testOnlyParamsUntyped(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsUntypedMixed(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsTyped(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsUndocumented(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsSuperfluousAnnotation(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsSuperfluousAnnotations(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsUntyped(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsTyped(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsDescription(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsMultilineDescription(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testComplexTypes(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testVariousMethodDeclarations(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsWithOtherAnnotationsInBetween(): void
    {
        $expected = <<<'EOT'
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
             * @param mixed $superflous1 With text
             * @param int $superflous2
             * @return array Long return
             *               description
             * @throws Exception
             * @throws FooException
             */
            function foo($a, $b) {}
            EOT;
        $input = <<<'EOT'
            <?php
            /**
             * [c1] Method description
             * [c2] over multiple lines
             *
             * @see Baz
             *
             * @param mixed $b
             * @param mixed $superflous1 With text
             * @return array Long return
             *               description
             * @param int $superflous2
             * @throws Exception
             * @param int   $a Long param
             *                 description
             * @throws FooException
             */
            function foo($a, $b) {}
            EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsBlankLines(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testNestedPhpdoc(): void
    {
        $expected = <<<'EOT'
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
            EOT;

        $input = <<<'EOT'
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
            EOT;

        $this->doTest($expected, $input);
    }

    public function testMultiNestedPhpdoc(): void
    {
        $expected = <<<'EOT'
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
            EOT;

        $input = <<<'EOT'
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
            EOT;

        $this->doTest($expected, $input);
    }

    public function testMultipleNestedPhpdoc(): void
    {
        $expected = <<<'EOT'
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
             * @param $superflous1 Superflous
             * @param $superflous2 Superflous
             *
             * @return bool
             */
            function string_array_walk(array &$array, callable $callback, $userdata = null, $foo) {}
            EOT;

        $input = <<<'EOT'
            <?php
            /**
             * @param $superflous1 Superflous
             * @param callable $callback {
             *     @param string $value
             *     @param int    $key
             *     @param mixed  $userdata {
             *         $param array $array
             *     }
             * }
             * @param $superflous2 Superflous
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
            EOT;

        $this->doTest($expected, $input);
    }

    public function testNonMatchingParamName(): void
    {
        $expected = <<<'EOT'
            <?php
            /**
             * @param Foo $fooBar
             * @param $fooSomethingNotMatchingTheName
             * @param OtherClassLorem $x
             */
            function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
            EOT;

        $input = <<<'EOT'
            <?php
            /**
             * @param $fooSomethingNotMatchingTheName
             * @param Foo $fooBar
             * @param OtherClassLorem $x
             */
            function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
            EOT;

        $this->doTest($expected, $input);
    }

    public function testPlainFunction(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testCommentsInSignature(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testClosure(): void
    {
        $expected = <<<'EOT'
            <?php
            /**
             * @param array $a
             * @param       $b
             * @param Foo   $c
             * @param int   $d
             */
            $closure = function (array $a, $b, Foo $c, $d) {};
            EOT;
        $input = <<<'EOT'
            <?php
            /**
             * @param       $b
             * @param int   $d
             * @param Foo   $c
             * @param array $a
             */
            $closure = function (array $a, $b, Foo $c, $d) {};
            EOT;
        $this->doTest($expected, $input);
    }

    public function testArrowFunction(): void
    {
        $expected = <<<'EOT'
            <?php
            /**
             * @param array $a
             * @param       $b
             * @param Foo   $c
             * @param int   $d
             */
            $closure = fn (array $a, $b, Foo $c, $d) => null;
            EOT;
        $input = <<<'EOT'
            <?php
            /**
             * @param       $b
             * @param int   $d
             * @param Foo   $c
             * @param array $a
             */
            $closure = fn (array $a, $b, Foo $c, $d) => null;
            EOT;
        $this->doTest($expected, $input);
    }

    public function testInterface(): void
    {
        $expected = <<<'EOT'
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
            EOT;
        $input = <<<'EOT'
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
            EOT;
        $this->doTest($expected, $input);
    }

    public function testPhp7ParamTypes(): void
    {
        $expected = <<<'EOT'
            <?php
            class C {
                /**
                 * @param array $a
                 * @param $b
                 * @param bool $c
                 */
                public function m(array $a, $b, bool $c) {}
            }
            EOT;
        $input = <<<'EOT'
            <?php
            class C {
                /**
                 * @param $b
                 * @param bool $c
                 * @param array $a
                 */
                public function m(array $a, $b, bool $c) {}
            }
            EOT;
        $this->doTest($expected, $input);
    }
}
