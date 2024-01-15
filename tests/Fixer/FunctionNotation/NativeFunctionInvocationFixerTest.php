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

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer
 */
final class NativeFunctionInvocationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureRejectsUnknownConfigurationKey(): void
    {
        $key = 'foo';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            '[native_function_invocation] Invalid configuration: The option "%s" does not exist.',
            $key
        ));

        $this->fixer->configure([
            $key => 'bar',
        ]);
    }

    /**
     * @dataProvider provideConfigureRejectsInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidConfigurationElement($element): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            'Each element must be a non-empty, trimmed string, got "%s" instead.',
            get_debug_type($element)
        ));

        $this->fixer->configure([
            'exclude' => [
                $element,
            ],
        ]);
    }

    public static function provideConfigureRejectsInvalidConfigurationElementCases(): iterable
    {
        yield 'null' => [null];

        yield 'false' => [false];

        yield 'true' => [false];

        yield 'int' => [1];

        yield 'array' => [[]];

        yield 'float' => [0.1];

        yield 'object' => [new \stdClass()];

        yield 'not-trimmed' => ['  is_string  '];
    }

    /**
     * @param string[] $include
     *
     * @dataProvider provideConfigureIncludeSetsCases
     */
    public function testConfigureIncludeSets(
        array $include,
        ?string $expectedExceptionClass = null,
        ?string $expectedExceptionMessage = null
    ): void {
        if (null !== $expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
            $this->expectExceptionMessageMatches(sprintf('#^%s$#', preg_quote($expectedExceptionMessage, '#')));
        }

        $this->fixer->configure(['include' => $include]);

        if (null === $expectedExceptionClass) {
            $this->addToAssertionCount(1);
        }
    }

    public static function provideConfigureIncludeSetsCases(): iterable
    {
        yield [['foo', 'bar']];

        yield [[NativeFunctionInvocationFixer::SET_ALL]];

        yield [[NativeFunctionInvocationFixer::SET_ALL, 'bar']];

        yield [
            ['@xxx'],
            InvalidFixerConfigurationException::class,
            '[native_function_invocation] Invalid configuration: Unknown set "@xxx", known sets are "@all", "@internal" and "@compiler_optimized".',
        ];

        yield [
            [' x '],
            InvalidFixerConfigurationException::class,
            '[native_function_invocation] Invalid configuration: Each element must be a non-empty, trimmed string, got "string" instead.',
        ];
    }

    public function testConfigureResetsExclude(): void
    {
        $this->fixer->configure([
            'exclude' => [
                'is_string',
            ],
        ]);

        $before = <<<'PHP'
            <?php

            namespace WithClassNotPrefixed;

            class Bar
            {
                public function baz($foo)
                {
                    if (isset($foo)) {
                        is_string($foo);
                    }
                }
            }
            PHP;

        $after = <<<'PHP'
            <?php

            namespace WithClassNotPrefixed;

            class Bar
            {
                public function baz($foo)
                {
                    if (isset($foo)) {
                        \is_string($foo);
                    }
                }
            }
            PHP;

        $this->doTest($before);

        $this->fixer->configure([]);

        $this->doTest($after, $before);
    }

    /**
     * @dataProvider provideFixWithDefaultConfigurationCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixWithDefaultConfigurationCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                \is_string($foo);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                \is_string($foo);

                EOD,
            <<<'EOD'
                <?php

                is_string($foo);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function bar($foo)
                    {
                        return \is_string($foo);
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                json_encode($foo);
                \strlen($foo);

                EOD,
            <<<'EOD'
                <?php

                json_encode($foo);
                strlen($foo);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function bar($foo)
                    {
                        return \IS_STRING($foo);
                    }
                }

                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    public function bar($foo)
                    {
                        return IS_STRING($foo);
                    }
                }

                EOD,
        ];

        yield 'fix multiple calls in single code' => [
            <<<'EOD'
                <?php

                json_encode($foo);
                \strlen($foo);
                \strlen($foo);

                EOD,
            <<<'EOD'
                <?php

                json_encode($foo);
                strlen($foo);
                strlen($foo);

                EOD,
        ];

        yield [
            '<?php $name = \get_class($foo, );',
            '<?php $name = get_class($foo, );',
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredExcludeCases
     */
    public function testFixWithConfiguredExclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'exclude' => [
                'is_string',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithConfiguredExcludeCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                is_string($foo);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function bar($foo)
                    {
                        return is_string($foo);
                    }
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithNamespaceConfigurationCases
     */
    public function testFixWithNamespaceConfiguration(string $expected, ?string $input = null, string $scope = 'namespaced'): void
    {
        $this->fixer->configure(['scope' => $scope]);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithNamespaceConfigurationCases(): iterable
    {
        yield [
            '<?php echo count([1]);',
        ];

        yield [
            <<<'EOD'
                <?php
                namespace space1 { ?>
                <?php echo \count([2]) ?>
                <?php }namespace {echo count([1]);}

                EOD,
            <<<'EOD'
                <?php
                namespace space1 { ?>
                <?php echo count([2]) ?>
                <?php }namespace {echo count([1]);}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Bar {
                    echo \strLEN("in 1");
                }

                namespace {
                    echo strlen("out 1");
                }

                namespace {
                    echo strlen("out 2");
                }

                namespace Bar{
                    echo \strlen("in 2");
                }

                namespace {
                    echo strlen("out 3");
                }

                EOD,
            <<<'EOD'
                <?php
                namespace Bar {
                    echo strLEN("in 1");
                }

                namespace {
                    echo strlen("out 1");
                }

                namespace {
                    echo strlen("out 2");
                }

                namespace Bar{
                    echo strlen("in 2");
                }

                namespace {
                    echo strlen("out 3");
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace space11 ?>

                    <?php
                echo \strlen(__NAMESPACE__);
                namespace space2;
                echo \strlen(__NAMESPACE__);

                EOD,
            <<<'EOD'
                <?php
                namespace space11 ?>

                    <?php
                echo strlen(__NAMESPACE__);
                namespace space2;
                echo strlen(__NAMESPACE__);

                EOD,
        ];

        yield [
            '<?php namespace PhpCsFixer\Tests\Fixer\Casing;\count([1]);',
            '<?php namespace PhpCsFixer\Tests\Fixer\Casing;count([1]);',
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Space12;

                echo \count([1]);

                namespace Space2;

                echo \count([1]);
                ?>

                EOD,
            <<<'EOD'
                <?php
                namespace Space12;

                echo count([1]);

                namespace Space2;

                echo count([1]);
                ?>

                EOD,
        ];

        yield [
            '<?php namespace {echo strlen("out 2");}',
        ];

        yield [
            <<<'EOD'
                <?php
                namespace space13 {
                    echo \strlen("in 1");
                }

                namespace space2 {
                    echo \strlen("in 2");
                }

                namespace { // global
                    echo strlen("global 1");
                }

                EOD,
            <<<'EOD'
                <?php
                namespace space13 {
                    echo strlen("in 1");
                }

                namespace space2 {
                    echo strlen("in 2");
                }

                namespace { // global
                    echo strlen("global 1");
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace space1 {
                    echo \count([1]);
                }
                namespace {
                    echo \count([1]);
                }

                EOD,
            <<<'EOD'
                <?php
                namespace space1 {
                    echo count([1]);
                }
                namespace {
                    echo count([1]);
                }

                EOD,
            'all',
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixWithConfiguredIncludeCases
     */
    public function testFixWithConfiguredInclude(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithConfiguredIncludeCases(): iterable
    {
        yield 'include set + 1, exclude 1' => [
            <<<'EOD'
                <?php
                                    echo \count([1]);
                                    \some_other($a, 3);
                                    echo strlen($a);
                                    not_me();
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo count([1]);
                                    some_other($a, 3);
                                    echo strlen($a);
                                    not_me();
                EOD."\n                ",
            [
                'include' => [NativeFunctionInvocationFixer::SET_INTERNAL, 'some_other'],
                'exclude' => ['strlen'],
            ],
        ];

        yield 'include @all' => [
            <<<'EOD'
                <?php
                                    echo \count([1]);
                                    \some_other($a, 3);
                                    echo \strlen($a);
                                    \me_as_well();
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo count([1]);
                                    some_other($a, 3);
                                    echo strlen($a);
                                    me_as_well();
                EOD."\n                ",
            [
                'include' => [NativeFunctionInvocationFixer::SET_ALL],
            ],
        ];

        yield 'include @compiler_optimized' => [
            <<<'EOD'
                <?php
                                    // do not fix
                                    $a = strrev($a);
                                    $a .= str_repeat($a, 4);
                                    $b = already_prefixed_function();
                                    // fix
                                    $c = \get_class($d);
                                    $e = \intval($f);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    // do not fix
                                    $a = strrev($a);
                                    $a .= str_repeat($a, 4);
                                    $b = \already_prefixed_function();
                                    // fix
                                    $c = get_class($d);
                                    $e = intval($f);
                EOD."\n                ",
            [
                'include' => [NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED],
            ],
        ];

        yield [
            <<<'EOD'
                <?php class Foo {
                                        public function & strlen($name) {
                                        }
                                    }
                EOD."\n                ",
        ];

        yield 'scope namespaced and strict enabled' => [
            <<<'EOD'
                <?php
                                    $a = not_compiler_optimized_function();
                                    $b = intval($c);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = \not_compiler_optimized_function();
                                    $b = \intval($c);
                EOD."\n                ",
            [
                'scope' => 'namespaced',
                'strict' => true,
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                                    use function foo\json_decode;
                                    json_decode($base);
                EOD."\n                ",
            null,
            [
                'include' => [NativeFunctionInvocationFixer::SET_ALL],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: string, 2?: array<mixed>}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'include @compiler_optimized with strict enabled' => [
            <<<'EOD'
                <?php
                                        $a = not_compiler_optimized_function();
                                        $b =  not_compiler_optimized_function();
                                        $c = \intval($d);
                EOD."\n                    ",
            <<<'EOD'
                <?php
                                        $a = \not_compiler_optimized_function();
                                        $b = \ not_compiler_optimized_function();
                                        $c = intval($d);
                EOD."\n                    ",
            [
                'include' => [NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED],
                'strict' => true,
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                echo \/**/strlen($a);
                echo \ strlen($a);
                echo \#
                #
                strlen($a);
                echo \strlen($a);

                EOD,
            <<<'EOD'
                <?php
                echo \/**/strlen($a);
                echo \ strlen($a);
                echo \#
                #
                strlen($a);
                echo strlen($a);

                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'attribute and strict' => [
            <<<'EOD'
                <?php
                                #[\Attribute(\Attribute::TARGET_CLASS)]
                                class Foo {}
                EOD."\n            ",
            null,
            ['strict' => true],
        ];

        yield 'null safe operator' => ['<?php $x?->count();'];

        yield 'multiple function-calls-like in attribute' => [
            <<<'EOD'
                <?php
                                #[Foo(), Bar(), Baz()]
                                class Foo {}
                EOD."\n            ",
            null,
            ['include' => ['@all']],
        ];
    }
}
