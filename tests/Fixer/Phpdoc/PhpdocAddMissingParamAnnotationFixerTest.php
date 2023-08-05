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

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer
 */
final class PhpdocAddMissingParamAnnotationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureRejectsUnknownConfigurationKey(): void
    {
        $key = 'foo';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
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
    public function testConfigureRejectsInvalidConfigurationValue($value, string $expectedMessage): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure([
            'only_untyped' => $value,
        ]);
    }

    /**
     * @return iterable<string, array>
     */
    public static function provideConfigureRejectsInvalidConfigurationValueCases(): iterable
    {
        yield 'null' => [
            null,
            '#expected to be of type "bool", but is of type "(null|NULL)"\.$#',
        ];

        yield 'int' => [
            1,
            '#expected to be of type "bool", but is of type "(int|integer)"\.$#',
        ];

        yield 'array' => [
            [],
            '#expected to be of type "bool", but is of type "array"\.$#',
        ];

        yield 'float' => [
            0.1,
            '#expected to be of type "bool", but is of type "(float|double)"\.$#',
        ];

        yield 'object' => [
            new \stdClass(),
            '#expected to be of type "bool", but is of type "stdClass"\.$#',
        ];
    }

    /**
     * @param null|array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?array $config = null): void
    {
        $this->fixer->configure($config ?? ['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
    /**
     *
     */',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
    class Foo {
        /**
         * @var int
         */
        private $foo;
    }',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
    /**
     * @param bool|bool[] $caseSensitive Line 1
     *                                   Line 2
     */
     function f11($caseSensitive) {}
',
        ];

        yield [
            '<?php
    /** @return string */
    function hello($string)
    {
        return $string;
    }',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
    /**
     * @{inheritdoc}
     */
    function f10(string $foo = "null", $bar) {}',
        ];

        yield [
            '<?php
    /**
     * @inheritDoc
     */
    function f10(string $foo = "null", $bar) {}',
        ];

        yield [
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
            ['only_untyped' => false],
        ];

        yield [
            '<?php
    /**
     * Foo
     * @param mixed $bar
     */
    function p1(?int $foo = 0, $bar) {}',
            '<?php
    /**
     * Foo
     */
    function p1(?int $foo = 0, $bar) {}',
            ['only_untyped' => true],
        ];

        yield [
            '<?php
    /**
     * Foo
     * @return int
     */
    function p1(?int $foo = 0) {}',
            null,
            ['only_untyped' => true],
        ];
    }

    /**
     * @param null|array<string, mixed> $config
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null, ?array $config = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($config ?? ['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            "<?php\r\n\t/**\r\n\t * @param int \$bar\r\n\t * @param null|string \$foo\r\n\t */\r\n\tfunction f7(string \$foo = nuLl, \$bar) {}",
            "<?php\r\n\t/**\r\n\t * @param int \$bar\r\n\t */\r\n\tfunction f7(string \$foo = nuLl, \$bar) {}",
        ];
    }

    /**
     * @dataProvider provideByReferenceCases
     */
    public function testByReference(string $expected, string $input): void
    {
        $this->fixer->configure(['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideByReferenceCases(): iterable
    {
        yield [
            '<?php
                    /**
                     * something
                     * @param mixed $numbers
                     */
                    function add(&$numbers) {}
                ',
            '<?php
                    /**
                     * something
                     */
                    function add(&$numbers) {}
                ',
        ];

        yield [
            '<?php
                    /**
                     * something
                     * @param null|array $numbers
                     */
                    function add(array &$numbers = null) {}
                ',
            '<?php
                    /**
                     * something
                     */
                    function add(array &$numbers = null) {}
                ',
        ];
    }

    /**
     * @dataProvider provideVariableNumberOfArgumentsCases
     */
    public function testVariableNumberOfArguments(string $expected, string $input): void
    {
        $this->fixer->configure(['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideVariableNumberOfArgumentsCases(): iterable
    {
        yield [
            '<?php
                    /**
                     * something
                     * @param array $numbers
                     */
                    function sum(...$numbers) {}
                ',
            '<?php
                    /**
                     * something
                     */
                    function sum(...$numbers) {}
                ',
        ];

        yield [
            '<?php
                    /**
                     * @param int $a
                     * @param array $numbers
                     */
                    function sum($a, ...$numbers) {}
                ',
            '<?php
                    /**
                     * @param int $a
                     */
                    function sum($a, ...$numbers) {}
                ',
        ];

        yield [
            '<?php
                    /**
                     * @param \Date[] $numbers
                     */
                    function sum(\Date ...$numbers) {}
                ',
            '<?php
                    /**
                     */
                    function sum(\Date ...$numbers) {}
                ',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php class Foo {
                /**
                 * @param Bar $x
                 * @param ?Bar $y
                 * @param null|Bar $z
                 */
                public function __construct(
                    public Bar $x,
                    protected ?Bar $y,
                    private null|Bar $z,
                ) {}
            }',
            '<?php class Foo {
                /**
                 */
                public function __construct(
                    public Bar $x,
                    protected ?Bar $y,
                    private null|Bar $z,
                ) {}
            }',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['only_untyped' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php class Foo {
                /**
                 * @param Bar $bar
                 * @param Baz $baz
                 */
                public function __construct(
                    public readonly Bar $bar,
                    readonly public Baz $baz,
                ) {}
            }',
            '<?php class Foo {
                /**
                 */
                public function __construct(
                    public readonly Bar $bar,
                    readonly public Baz $baz,
                ) {}
            }',
        ];
    }
}
