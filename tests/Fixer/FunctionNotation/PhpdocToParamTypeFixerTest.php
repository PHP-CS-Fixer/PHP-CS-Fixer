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
     * @param string      $expected
     * @param null|string $input
     * @param null|int    $versionSpecificFix
     * @param null|array  $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, $versionSpecificFix = null, $config = null)
    {
        if (
            (null !== $input && \PHP_VERSION_ID < 70000)
            || (null !== $versionSpecificFix && \PHP_VERSION_ID < $versionSpecificFix)
        ) {
            $expected = $input;
            $input = null;
        }
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'no phpdoc param' => [
                '<?php function my_foo() {}',
            ],
            'invalid param' => [
                '<?php /** @param */ function my_foo() {}',
            ],
            'invalid class 1' => [
                '<?php /** @param \9 */ function my_foo() {}',
            ],
            'invalid class 2' => [
                '<?php /** @param \\Foo\\\\Bar */ function my_foo() {}',
            ],
            'blacklisted class methods' => [
                '<?php

                    class Foo
                    {
                        /** @param Bar */
                        function __construct() {}
                        /** @param Bar */
                        function __destruct() {}
                        /** @param Bar */
                        function __clone() {}
                    }
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
            'non-root class with multiple string param' => [
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
            'invalid void param on ^7.1' => [
                '<?php /** @param null|void $foo */ function my_foo($foo) {}',
            ],
            'iterable return on ^7.1' => [
                '<?php /** @param iterable $counter */ function my_foo(iterable $counter) {}',
                '<?php /** @param iterable $counter */ function my_foo($counter) {}',
                70100,
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
            'static is skipped' => [
                '<?php
                    class Foo {
                        /** @param static $foo */ function my_foo($foo) {}
                    }
                ',
            ],
            'skip resource special type' => [
                '<?php /** @param resource */ function my_foo() {}',
            ],
            'skip mixed special type' => [
                '<?php /** @param mixed */ function my_foo() {}',
            ],
            'null alone cannot be a param type' => [
                '<?php /** @param null */ function my_foo() {}',
            ],
            'skip mixed types' => [
                '<?php /** @param Foo|Bar */ function my_foo() {}',
            ],
            'array of types' => [
                '<?php /** @param Foo[] $foo */ function my_foo(array $foo) {}',
                '<?php /** @param Foo[] $foo */ function my_foo($foo) {}',
            ],
            'nullable array of types' => [
                '<?php /** @param null|Foo[] $foo */ function my_foo(?array $foo) {}',
                '<?php /** @param null|Foo[] $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and string param' => [
                '<?php /** @param null|string $foo */ function my_foo(?string $foo) {}',
                '<?php /** @param null|string $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and int param' => [
                '<?php /** @param null|int $foo */ function my_foo(?int $foo) {}',
                '<?php /** @param null|int $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and float param' => [
                '<?php /** @param null|float $foo */ function my_foo(?float $foo) {}',
                '<?php /** @param null|float $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and bool param' => [
                '<?php /** @param null|bool $foo */ function my_foo(?bool $foo) {}',
                '<?php /** @param null|bool $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and callable param' => [
                '<?php /** @param null|callable $foo */ function my_foo(?callable $foo) {}',
                '<?php /** @param null|callable $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and iterable param' => [
                '<?php /** @param null|iterable $foo */ function my_foo(?iterable $foo) {}',
                '<?php /** @param null|iterable $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and class name param' => [
                '<?php /** @param null|Foo $foo */ function my_foo(?Foo $foo) {}',
                '<?php /** @param null|Foo $foo */ function my_foo($foo) {}',
                70100,
            ],
            'nullable and object param' => [
                '<?php /** @param null|object $foo */ function my_foo(?object $foo) {}',
                '<?php /** @param null|object $foo */ function my_foo($foo) {}',
                70200,
            ],
        ];
    }
}
