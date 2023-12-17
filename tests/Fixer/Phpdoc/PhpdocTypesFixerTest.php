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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer
 */
final class PhpdocTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param array<string, mixed> $configuration
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'windows line breaks' => [
            "<?php /**\r\n * @param string|string[] \$bar\r\n *\r\n * @return int[]\r\n */\r\n",
            "<?php /**\r\n * @param STRING|String[] \$bar\r\n *\r\n * @return inT[]\r\n */\r\n",
        ];

        yield 'conversion' => [
            '<?php
    /**
     * @param boolean|array|Foo $bar
     *
     * @return int|float
     */

',
            '<?php
    /**
     * @param Boolean|Array|Foo $bar
     *
     * @return inT|Float
     */

',
        ];

        yield 'array stuff' => [
            '<?php
    /**
     * @param string|string[] $bar
     *
     * @return int[]
     */

',
            '<?php
    /**
     * @param STRING|String[] $bar
     *
     * @return inT[]
     */

',
        ];

        yield 'nested array stuff' => [
            '<?php
    /**
     * @return int[][][]
     */
',
            '<?php
    /**
     * @return INT[][][]
     */
',
        ];

        yield 'mixed and void' => [
            '<?php
    /**
     * @param mixed $foo
     *
     * @return void
     */

',
            '<?php
    /**
     * @param Mixed $foo
     *
     * @return Void
     */

',
        ];

        yield 'iterable' => [
            '<?php
    /**
     * @param iterable $foo
     *
     * @return Itter
     */

',
            '<?php
    /**
     * @param Iterable $foo
     *
     * @return Itter
     */

',
        ];

        yield 'method and property' => [
            '<?php
/**
 * @method self foo()
 * @property int $foo
 * @property-read boolean $bar
 * @property-write mixed $baz
 */

',
            '<?php
/**
 * @method Self foo()
 * @property Int $foo
 * @property-read Boolean $bar
 * @property-write MIXED $baz
 */

',
        ];

        yield 'throws' => [
            '<?php
/**
 * @throws static
 */

',
            '<?php
/**
 * @throws STATIC
 */

',
        ];

        yield 'inline doc' => [
            '<?php
    /**
     * Does stuff with stuffs.
     *
     * @param array $stuffs {
     *     @var bool $foo
     *     @var int  $bar
     * }
     */

',
            '<?php
    /**
     * Does stuff with stuffs.
     *
     * @param array $stuffs {
     *     @var Bool $foo
     *     @var INT  $bar
     * }
     */

',
        ];

        yield 'with config' => [
            '<?php
    /**
     * @param self|array|Foo $bar
     *
     * @return int|float|boolean|Double
     */

',
            '<?php
    /**
     * @param SELF|Array|Foo $bar
     *
     * @return inT|Float|boolean|Double
     */

',
            ['groups' => ['simple', 'meta']],
        ];

        yield 'generics' => [
            '<?php
            /**
             * @param array<int, object> $a
             * @param array<iterable> $b
             * @param array<parent|$this|self> $c
             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
             *
             * @return array<int, array<string, array<int, DoNotChangeThisAsThisIsAClass>>>
             */',
            '<?php
            /**
             * @param ARRAY<INT, OBJECT> $a
             * @param ARRAY<ITERABLE> $b
             * @param array<Parent|$This|Self> $c
             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
             *
             * @return ARRAY<INT, ARRAY<STRING, ARRAY<INT, DoNotChangeThisAsThisIsAClass>>>
             */',
            ['groups' => ['simple', 'meta']],
        ];

        yield 'callable' => [
            '<?php /**
                    * @param callable() $a
                    * @param callable(): void $b
                    * @param callable(bool, int, string): float $c
                    */',
            '<?php /**
                    * @param CALLABLE() $a
                    * @param Callable(): VOID $b
                    * @param CALLABLE(BOOL, INT, STRING): FLOAT $c
                    */',
        ];

        yield 'array shape with key name being also type name' => [
            '<?php /**
                    * @return array{FOO: bool, NULL: null|int, BAR: string|BAZ}
                    */',
            '<?php /**
                    * @return ARRAY{FOO: BOOL, NULL: NULL|INT, BAR: STRING|BAZ}
                    */',
        ];

        yield 'union with \'NULL\'' => [
            '<?php /**
                    * @return \'NULL\'|null|false
                    */',
            '<?php /**
                    * @return \'NULL\'|NULL|false
                    */',
        ];

        yield 'union with "NULL"' => [
            '<?php /**
                    * @return null|"NULL"|false
                    */',
            '<?php /**
                    * @return NULL|"NULL"|false
                    */',
        ];

        yield 'method with reserved identifier' => [
            '<?php /**
                    * @method bool BOOL(): void
                    */',
            '<?php /**
                    * @method BOOL BOOL(): void
                    */',
        ];

        yield 'no space between type and variable' => [
            '<?php /** @param null|string$foo */',
            '<?php /** @param NULL|STRING$foo */',
        ];

        yield '"Callback" class in phpdoc must not be lowered' => [
            '<?php
    /**
     * @param Callback $foo
     *
     * @return Callback
     */
',
        ];

        yield 'param with extra chevrons' => [
            '<?php /** @param array <3> $value */',
            '<?php /** @param ARRAY <3> $value */',
        ];

        yield 'param with extra parentheses' => [
            '<?php /** @param \Closure (int) $value */',
            '<?php /** @param \Closure (INT) $value */',
        ];

        yield 'param with union type and extra parentheses' => [
            '<?php /** @param \Closure (float|int) $value */',
            '<?php /** @param \Closure (FLOAT|INT) $value */',
        ];

        yield 'return with union type and extra parentheses' => [
            '<?php /** @return float|int (number) count of something */',
            '<?php /** @return FLOAT|INT (number) count of something */',
        ];
    }

    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[phpdoc_types\] Invalid configuration: The option "groups" .*\.$/');

        $this->fixer->configure(['groups' => ['__TEST__']]);
    }
}
