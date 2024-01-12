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
            <<<'EOD'
                <?php
                    /**
                     * @param boolean|array|Foo $bar
                     *
                     * @return int|float
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @param Boolean|Array|Foo $bar
                     *
                     * @return inT|Float
                     */


                EOD,
        ];

        yield 'array stuff' => [
            <<<'EOD'
                <?php
                    /**
                     * @param string|string[] $bar
                     *
                     * @return int[]
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @param STRING|String[] $bar
                     *
                     * @return inT[]
                     */


                EOD,
        ];

        yield 'nested array stuff' => [
            <<<'EOD'
                <?php
                    /**
                     * @return int[][][]
                     */

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @return INT[][][]
                     */

                EOD,
        ];

        yield 'mixed and void' => [
            <<<'EOD'
                <?php
                    /**
                     * @param mixed $foo
                     *
                     * @return void
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @param Mixed $foo
                     *
                     * @return Void
                     */


                EOD,
        ];

        yield 'iterable' => [
            <<<'EOD'
                <?php
                    /**
                     * @param iterable $foo
                     *
                     * @return Itter
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @param Iterable $foo
                     *
                     * @return Itter
                     */


                EOD,
        ];

        yield 'method and property' => [
            <<<'EOD'
                <?php
                /**
                 * @method self foo()
                 * @property int $foo
                 * @property-read boolean $bar
                 * @property-write mixed $baz
                 */


                EOD,
            <<<'EOD'
                <?php
                /**
                 * @method Self foo()
                 * @property Int $foo
                 * @property-read Boolean $bar
                 * @property-write MIXED $baz
                 */


                EOD,
        ];

        yield 'throws' => [
            <<<'EOD'
                <?php
                /**
                 * @throws static
                 */


                EOD,
            <<<'EOD'
                <?php
                /**
                 * @throws STATIC
                 */


                EOD,
        ];

        yield 'inline doc' => [
            <<<'EOD'
                <?php
                    /**
                     * Does stuff with stuffs.
                     *
                     * @param array $stuffs {
                     *     @var bool $foo
                     *     @var int  $bar
                     * }
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Does stuff with stuffs.
                     *
                     * @param array $stuffs {
                     *     @var Bool $foo
                     *     @var INT  $bar
                     * }
                     */


                EOD,
        ];

        yield 'with config' => [
            <<<'EOD'
                <?php
                    /**
                     * @param self|array|Foo $bar
                     *
                     * @return int|float|boolean|Double
                     */


                EOD,
            <<<'EOD'
                <?php
                    /**
                     * @param SELF|Array|Foo $bar
                     *
                     * @return inT|Float|boolean|Double
                     */


                EOD,
            ['groups' => ['simple', 'meta']],
        ];

        yield 'generics' => [
            <<<'EOD'
                <?php
                            /**
                             * @param array<int, object> $a
                             * @param array<iterable> $b
                             * @param array<parent|$this|self> $c
                             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
                             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
                             *
                             * @return array<int, array<string, array<int, DoNotChangeThisAsThisIsAClass>>>
                             */
                EOD,
            <<<'EOD'
                <?php
                            /**
                             * @param ARRAY<INT, OBJECT> $a
                             * @param ARRAY<ITERABLE> $b
                             * @param array<Parent|$This|Self> $c
                             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
                             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
                             *
                             * @return ARRAY<INT, ARRAY<STRING, ARRAY<INT, DoNotChangeThisAsThisIsAClass>>>
                             */
                EOD,
            ['groups' => ['simple', 'meta']],
        ];

        yield 'callable' => [
            <<<'EOD'
                <?php /**
                                    * @param callable() $a
                                    * @param callable(): void $b
                                    * @param callable(bool, int, string): float $c
                                    */
                EOD,
            <<<'EOD'
                <?php /**
                                    * @param CALLABLE() $a
                                    * @param Callable(): VOID $b
                                    * @param CALLABLE(BOOL, INT, STRING): FLOAT $c
                                    */
                EOD,
        ];

        yield 'array shape with key name being also type name' => [
            <<<'EOD'
                <?php /**
                                    * @return array{FOO: bool, NULL: null|int, BAR: string|BAZ}
                                    */
                EOD,
            <<<'EOD'
                <?php /**
                                    * @return ARRAY{FOO: BOOL, NULL: NULL|INT, BAR: STRING|BAZ}
                                    */
                EOD,
        ];

        yield 'union with \'NULL\'' => [
            <<<'EOD'
                <?php /**
                                    * @return 'NULL'|null|false
                                    */
                EOD,
            <<<'EOD'
                <?php /**
                                    * @return 'NULL'|NULL|false
                                    */
                EOD,
        ];

        yield 'union with "NULL"' => [
            <<<'EOD'
                <?php /**
                                    * @return null|"NULL"|false
                                    */
                EOD,
            <<<'EOD'
                <?php /**
                                    * @return NULL|"NULL"|false
                                    */
                EOD,
        ];

        yield 'method with reserved identifier' => [
            <<<'EOD'
                <?php /**
                                    * @method bool BOOL(): void
                                    */
                EOD,
            <<<'EOD'
                <?php /**
                                    * @method BOOL BOOL(): void
                                    */
                EOD,
        ];

        yield 'no space between type and variable' => [
            '<?php /** @param null|string$foo */',
            '<?php /** @param NULL|STRING$foo */',
        ];

        yield '"Callback" class in phpdoc must not be lowered' => [
            <<<'EOD'
                <?php
                    /**
                     * @param Callback $foo
                     *
                     * @return Callback
                     */

                EOD,
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
