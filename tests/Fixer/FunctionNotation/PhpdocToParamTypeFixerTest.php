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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Jan Gantzert <jan@familie-gantzert.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\PhpdocToParamTypeFixer
 */
final class PhpdocToParamTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?int $versionSpecificFix = null, array $config = []): void
    {
        if (
            null !== $input
            && (null !== $versionSpecificFix && \PHP_VERSION_ID < $versionSpecificFix)
        ) {
            $expected = $input;
            $input = null;
        }

        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'typehint already defined' => [
                '<?php /** @param int $foo */ function foo(int $foo) {}',
            ],
            'typehint already defined with wrong phpdoc typehint' => [
                '<?php /** @param string $foo */ function foo(int $foo) {}',
            ],
            'no phpdoc param' => [
                '<?php function my_foo() {}',
            ],
            'invalid - phpdoc param without variable' => [
                '<?php /** @param */ function my_foo($bar) {}',
            ],
            'invalid - phpdoc param with non existing class' => [
                '<?php /** @param \9 */ function my_foo($bar) {}',
            ],
            'invalid - phpdoc param with false class hint' => [
                '<?php /** @param $foo \\Foo\\\\Bar */ function my_foo($foo) {}',
            ],
            'invalid - phpdoc param with false param order' => [
                '<?php /** @param $foo string */ function my_foo($foo) {}',
            ],
            'invalid - phpdoc param with hint for next method' => [
                '<?php
                    /**
                    * @param string $bar
                    */
                    function my_foo() {}
                    function my_foo2($bar) {}
                    ',
            ],
            'invalid - phpdoc param with keyword' => [
                '<?php
                    /** @param Break $foo */ function foo_break($foo) {}
                    /** @param __CLASS__ $foo */ function foo_class($foo) {}
                    /** @param I\Want\To\Break\\\\Free $foo */ function foo_queen($foo) {}
                ',
            ],
            'non-root class with single int param' => [
                '<?php /** @param int $bar */ function my_foo(int $bar) {}',
                '<?php /** @param int $bar */ function my_foo($bar) {}',
            ],
            'non-root class with single float param' => [
                '<?php /** @param float $bar */ function my_foo(float $bar) {}',
                '<?php /** @param float $bar */ function my_foo($bar) {}',
            ],
            'non-root class with multiple string params' => [
                '<?php
                    /**
                    * @param string $bar
                    * @param string $baz
                    */
                    function my_foo(string $bar, string $baz) {}',
                '<?php
                    /**
                    * @param string $bar
                    * @param string $baz
                    */
                    function my_foo($bar, $baz) {}',
            ],
            'non-root class with not sorted multiple string params' => [
                '<?php
                    /**
                    * @param int $foo
                    * @param string $bar
                    */
                    function my_foo(string $bar, int $foo) {}',
                '<?php
                    /**
                    * @param int $foo
                    * @param string $bar
                    */
                    function my_foo($bar, $foo) {}',
            ],
            'non-root class with not sorted multiple params and different types' => [
                '<?php
                    /**
                    * @param int $foo
                    * @param string $bar
                    * @param Baz $hey
                    * @param float $tab
                    * @param bool $baz
                    */
                    function my_foo(string $bar, int $foo, bool $baz, float $tab, Baz $hey) {}',
                '<?php
                    /**
                    * @param int $foo
                    * @param string $bar
                    * @param Baz $hey
                    * @param float $tab
                    * @param bool $baz
                    */
                    function my_foo($bar, $foo, $baz, $tab, $hey) {}',
            ],
            'non-root class with massive string params' => [
                '<?php
                    /**
                    * @param string $bar
                    * @param string $baz
                    * @param string $tab
                    * @param string $foo
                    */
                    function my_foo(string $bar, string $baz, string $tab, string $foo) {}',
                '<?php
                    /**
                    * @param string $bar
                    * @param string $baz
                    * @param string $tab
                    * @param string $foo
                    */
                    function my_foo($bar, $baz, $tab, $foo) {}',
            ],
            'non-root class with different types of params' => [
                '<?php
                    /**
                    * @param string $bar
                    * @param int $baz
                    * @param float $tab
                    */
                    function my_foo(string $bar, int $baz, float $tab) {}',
                '<?php
                    /**
                    * @param string $bar
                    * @param int $baz
                    * @param float $tab
                    */
                    function my_foo($bar, $baz, $tab) {}',
            ],
            'non-root class with mixed type of param' => [
                '<?php
                    /**
                    * @param mixed $bar
                    */
                    function my_foo($bar) {}',
            ],
            'non-root namespaced class' => [
                '<?php /** @param My\Bar $foo */ function my_foo(My\Bar $foo) {}',
                '<?php /** @param My\Bar $foo */ function my_foo($foo) {}',
            ],
            'root class' => [
                '<?php /** @param \My\Bar $foo */ function my_foo(\My\Bar $foo) {}',
                '<?php /** @param \My\Bar $foo */ function my_foo($foo) {}',
            ],
            'interface' => [
                '<?php interface Foo { /** @param Bar $bar */ function my_foo(Bar $bar); }',
                '<?php interface Foo { /** @param Bar $bar */ function my_foo($bar); }',
            ],
            'iterable return on ^7.1' => [
                '<?php /** @param iterable $counter */ function my_foo(iterable $counter) {}',
                '<?php /** @param iterable $counter */ function my_foo($counter) {}',
            ],
            'array native type' => [
                '<?php /** @param array $foo */ function my_foo(array $foo) {}',
                '<?php /** @param array $foo */ function my_foo($foo) {}',
            ],
            'callable type' => [
                '<?php /** @param callable $foo */ function my_foo(callable $foo) {}',
                '<?php /** @param callable $foo */ function my_foo($foo) {}',
            ],
            'self accessor' => [
                '<?php
                    class Foo {
                        /** @param self $foo */ function my_foo(self $foo) {}
                    }
                ',
                '<?php
                    class Foo {
                        /** @param self $foo */ function my_foo($foo) {}
                    }
                ',
            ],
            'report static as self' => [
                '<?php
                    class Foo {
                        /** @param static $foo */ function my_foo(self $foo) {}
                    }
                ',
                '<?php
                    class Foo {
                        /** @param static $foo */ function my_foo($foo) {}
                    }
                ',
            ],
            'skip resource special type' => [
                '<?php /** @param $bar resource */ function my_foo($bar) {}',
            ],
            'skip mixed special type' => [
                '<?php /** @param $bar mixed */ function my_foo($bar) {}',
            ],
            'null alone cannot be a param type' => [
                '<?php /** @param $bar null */ function my_foo($bar) {}',
            ],
            'skip mixed types' => [
                '<?php /** @param Foo|Bar $bar */ function my_foo($bar) {}',
            ],
            'skip mixed types including array' => [
                '<?php /** @param array|Foo $expected */ function testResolveIntersectionOfPaths($expected) {}',
            ],
            'skip primitive or array types' => [
                '<?php /** @param string|string[] $expected */ function testResolveIntersectionOfPaths($expected) {}',
            ],
            'array of types' => [
                '<?php /** @param Foo[] $foo */ function my_foo(array $foo) {}',
                '<?php /** @param Foo[] $foo */ function my_foo($foo) {}',
            ],
            'nullable array of types' => [
                '<?php /** @param null|Foo[] $foo */ function my_foo(?array $foo) {}',
                '<?php /** @param null|Foo[] $foo */ function my_foo($foo) {}',
            ],
            'nullable and mixed types of arrays' => [
                '<?php /** @param null|Foo[]|Bar[] $foo */ function my_foo(?array $foo) {}',
                '<?php /** @param null|Foo[]|Bar[] $foo */ function my_foo($foo) {}',
            ],
            'nullable and array and array of types' => [
                '<?php /** @param null|Foo[]|array $foo */ function my_foo(?array $foo) {}',
                '<?php /** @param null|Foo[]|array $foo */ function my_foo($foo) {}',
            ],
            'nullable array of array of types' => [
                '<?php /** @param null|Foo[][] $foo */ function my_foo(?array $foo) {}',
                '<?php /** @param null|Foo[][] $foo */ function my_foo($foo) {}',
            ],
            'nullable and string param' => [
                '<?php /** @param null|string $foo */ function my_foo(?string $foo) {}',
                '<?php /** @param null|string $foo */ function my_foo($foo) {}',
            ],
            'nullable and int param' => [
                '<?php /** @param null|int $foo */ function my_foo(?int $foo) {}',
                '<?php /** @param null|int $foo */ function my_foo($foo) {}',
            ],
            'nullable and float param' => [
                '<?php /** @param null|float $foo */ function my_foo(?float $foo) {}',
                '<?php /** @param null|float $foo */ function my_foo($foo) {}',
            ],
            'nullable and bool param' => [
                '<?php /** @param null|bool $foo */ function my_foo(?bool $foo) {}',
                '<?php /** @param null|bool $foo */ function my_foo($foo) {}',
            ],
            'nullable and callable param' => [
                '<?php /** @param null|callable $foo */ function my_foo(?callable $foo) {}',
                '<?php /** @param null|callable $foo */ function my_foo($foo) {}',
            ],
            'nullable and iterable param' => [
                '<?php /** @param null|iterable $foo */ function my_foo(?iterable $foo) {}',
                '<?php /** @param null|iterable $foo */ function my_foo($foo) {}',
            ],
            'nullable and class name param' => [
                '<?php /** @param null|Foo $foo */ function my_foo(?Foo $foo) {}',
                '<?php /** @param null|Foo $foo */ function my_foo($foo) {}',
            ],
            'array and iterable param' => [
                '<?php /** @param Foo[]|iterable $foo */ function my_foo(iterable $foo) {}',
                '<?php /** @param Foo[]|iterable $foo */ function my_foo($foo) {}',
            ],
            'object param' => [
                '<?php /** @param object $foo */ function my_foo(object $foo) {}',
                '<?php /** @param object $foo */ function my_foo($foo) {}',
                7_02_00,
            ],
            'nullable and object param' => [
                '<?php /** @param null|object $foo */ function my_foo(?object $foo) {}',
                '<?php /** @param null|object $foo */ function my_foo($foo) {}',
                7_02_00,
            ],
            'generics with single type' => [
                '<?php /** @param array<foo> $foo */ function my_foo(array $foo) {}',
                '<?php /** @param array<foo> $foo */ function my_foo($foo) {}',
            ],
            'generics with multiple types' => [
                '<?php /** @param array<int, string> $foo */ function my_foo(array $foo) {}',
                '<?php /** @param array<int, string> $foo */ function my_foo($foo) {}',
            ],
            'stop searching last token' => [
                '<?php class Foo { /** @param Bar $bar */ public function foo($tab) { } }',
            ],
            'param by reference' => [
                '<?php /** @param array $data */ function foo(array &$data) {}',
                '<?php /** @param array $data */ function foo(&$data) {}',
            ],
            'optional param by reference' => [
                '<?php /** @param null|string[] $matches */ function matchAll(?array &$matches) {}',
                '<?php /** @param null|string[] $matches */ function matchAll(&$matches) {}',
            ],
            'void as type in phpdoc' => [
                '<?php /** @param void $bar */ function foo($bar) {}',
            ],
            'array and traversable' => [
                '<?php /** @param array|Traversable $foo */ function my_foo(iterable $foo) {}',
                '<?php /** @param array|Traversable $foo */ function my_foo($foo) {}',
            ],
            'array and traversable with leading slash' => [
                '<?php /** @param array|\Traversable $foo */ function my_foo(iterable $foo) {}',
                '<?php /** @param array|\Traversable $foo */ function my_foo($foo) {}',
            ],
            'array and traversable in a namespace' => [
                '<?php
                     namespace App;
                     /** @param array|Traversable $foo */
                     function my_foo($foo) {}
                ',
            ],
            'array and traversable with leading slash in a namespace' => [
                '<?php
                     namespace App;
                     /** @param array|\Traversable $foo */
                     function my_foo(iterable $foo) {}
                ',
                '<?php
                     namespace App;
                     /** @param array|\Traversable $foo */
                     function my_foo($foo) {}
                ',
            ],
            'array and imported traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Traversable;
                     /** @param array|Traversable $foo */
                     function my_foo(iterable $foo) {}
                ',
                '<?php
                     namespace App;
                     use Traversable;
                     /** @param array|Traversable $foo */
                     function my_foo($foo) {}
                ',
            ],
            'array and object aliased as traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Foo as Traversable;
                     /** @param array|Traversable $foo */
                     function my_foo($foo) {}
                ',
            ],
            'array of object and traversable' => [
                '<?php /** @param Foo[]|Traversable $foo */ function my_foo(iterable $foo) {}',
                '<?php /** @param Foo[]|Traversable $foo */ function my_foo($foo) {}',
                7_01_00,
            ],
            'array of object and iterable' => [
                '<?php /** @param Foo[]|iterable $foo */ function my_foo(iterable $foo) {}',
                '<?php /** @param Foo[]|iterable $foo */ function my_foo($foo) {}',
            ],
            'array of string and array of int' => [
                '<?php /** @param string[]|int[] $foo */ function my_foo(array $foo) {}',
                '<?php /** @param string[]|int[] $foo */ function my_foo($foo) {}',
            ],
            'do not fix scalar types when configured as such' => [
                '<?php /** @param int $foo */ function my_foo($foo) {}',
                null,
                null,
                ['scalar_types' => false],
            ],
            'do not fix function call' => [
                '<?php
                    /** @param string $foo */
                    function bar($notFoo) {
                        return baz($foo);
                    }
                ',
            ],
            'do not fix function call when no parameter' => [
                '<?php
                    /** @param string $foo */
                    function bar() {
                        return baz($foo);
                    }
                ',
            ],
            'intersection types' => [
                '<?php
                    /** @param Bar&Baz $x */
                    function bar($x) {}
                ',
            ],
            'very long class name before ampersand' => [
                '<?php
                    /** @param Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar&Baz $x */
                    function bar($x) {}
                ',
            ],
            'very long class name after ampersand' => [
                '<?php
                    /** @param Bar&Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaz $x */
                    function bar($x) {}
                ',
            ],
        ];
    }
}
