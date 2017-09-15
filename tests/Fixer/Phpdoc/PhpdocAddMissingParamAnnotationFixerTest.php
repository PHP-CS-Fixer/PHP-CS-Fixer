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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer
 */
final class PhpdocAddMissingParamAnnotationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureRejectsUnknownConfigurationKey()
    {
        $key = 'foo';

        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, sprintf(
            '[phpdoc_add_missing_param_annotation] Invalid configuration: The option "%s" does not exist.',
            $key
        ));

        $this->fixer->configure([
            $key => 'bar',
        ]);
    }

    /**
     * @dataProvider provideConfigureRejectsInvalidConfigurationValueCases
     *
     * @param mixed $value
     */
    public function testConfigureRejectsInvalidConfigurationValue($value)
    {
        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, sprintf(
            'expected to be of type "bool", but is of type "%s".',
            is_object($value) ? get_class($value) : gettype($value)
        ));

        $this->fixer->configure([
            'only_untyped' => $value,
        ]);
    }

    /**
     * @return array
     */
    public function provideConfigureRejectsInvalidConfigurationValueCases()
    {
        return [
            'null' => [null],
            'int' => [1],
            'array' => [[]],
            'float' => [0.1],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = null)
    {
        $this->fixer->configure($config ?: ['only_untyped' => false]);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
    /**
     *
     */',
            ],
            [
                '<?php
    /**
     * @param int $foo
     * @param mixed $bar
     */
    function f1($foo, $bar) {}',
                '<?php
    /**
     * @param int $foo
     */
    function f1($foo, $bar) {}',
            ],
            [
                '<?php
    /**
     * @param int $bar
     * @param mixed $foo
     */
    function f2($foo, $bar) {}',
                '<?php
    /**
     * @param int $bar
     */
    function f2($foo, $bar) {}',
            ],
            [
                '<?php
    /**
     * @return void
     * @param mixed $foo
     * @param mixed $bar
     */
    function f3($foo, $bar) {}',
                '<?php
    /**
     * @return void
     */
    function f3($foo, $bar) {}',
            ],
            [
                '<?php
    abstract class Foo {
        /**
         * @param int $bar
         * @param mixed $foo
         */
        abstract public function f4a($foo, $bar);
    }',
                '<?php
    abstract class Foo {
        /**
         * @param int $bar
         */
        abstract public function f4a($foo, $bar);
    }',
            ],
            [
                '<?php
    class Foo {
        /**
         * @param int $bar
         * @param mixed $foo
         */
        static final public function f4b($foo, $bar) {}
    }',
                '<?php
    class Foo {
        /**
         * @param int $bar
         */
        static final public function f4b($foo, $bar) {}
    }',
            ],
            [
                '<?php
    class Foo {
        /**
         * @var int
         */
        private $foo;
    }',
            ],
            [
                '<?php
    /**
     * @param $bar No type !!
     * @param mixed $foo
     */
    function f5($foo, $bar) {}',
                '<?php
    /**
     * @param $bar No type !!
     */
    function f5($foo, $bar) {}',
            ],
            [
                '<?php
    /**
     * @param int
     * @param int $bar
     * @param Foo\Bar $foo
     */
    function f6(Foo\Bar $foo, $bar) {}',
                '<?php
    /**
     * @param int
     * @param int $bar
     */
    function f6(Foo\Bar $foo, $bar) {}',
            ],
            [
                '<?php
    /**
     * @param int $bar
     * @param null|string $foo
     */
    function f7(string $foo = nuLl, $bar) {}',
                '<?php
    /**
     * @param int $bar
     */
    function f7(string $foo = nuLl, $bar) {}',
            ],
            [
                '<?php
    /**
     * @param int $bar
     * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}',
                '<?php
    /**
     * @param int $bar
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}',
                ['only_untyped' => true],
            ],
            [
                '<?php
    /**
     * @param bool|bool[] $caseSensitive Line 1
     *                                   Line 2
     */
     function f11($caseSensitive) {}
',
            ],
            [
                '<?php
    /** @return string */
    function hello($string)
    {
        return $string;
    }',
            ],
            [
                '<?php
    /** @return string
     * @param mixed $string
     */
    function hello($string)
    {
        return $string;
    }',
                '<?php
    /** @return string
     */
    function hello($string)
    {
        return $string;
    }',
            ],
            [
                '<?php
    /**
     * @param mixed $string
     * @return string */
    function hello($string)
    {
        return $string;
    }',
                '<?php
    /**
     * @return string */
    function hello($string)
    {
        return $string;
    }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null, array $config = null)
    {
        $this->fixer->configure($config ?: ['only_untyped' => false]);

        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            [
                '<?php
    /**
     * @param int $bar
     * @param string $foo
     */
    function f8(string $foo = "null", $bar) {}',
                '<?php
    /**
     * @param int $bar
     */
    function f8(string $foo = "null", $bar) {}',
            ],
            [
                '<?php
    /**
     * @{inheritdoc}
     */
    function f10(string $foo = "null", $bar) {}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input = null, array $config = null)
    {
        $this->fixer->configure($config ?: ['only_untyped' => false]);

        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
    {
        return [
            [
                '<?php
    /**
     * @param int $bar
     * @param ?array $foo
     */
    function p1(?array $foo = null, $bar) {}',
                '<?php
    /**
     * @param int $bar
     */
    function p1(?array $foo = null, $bar) {}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null, array $config = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($config ?: ['only_untyped' => false]);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\r\n\t/**\r\n\t * @param int \$bar\r\n\t * @param null|string \$foo\r\n\t */\r\n\tfunction f7(string \$foo = nuLl, \$bar) {}",
                "<?php\r\n\t/**\r\n\t * @param int \$bar\r\n\t */\r\n\tfunction f7(string \$foo = nuLl, \$bar) {}",
            ],
        ];
    }
}
