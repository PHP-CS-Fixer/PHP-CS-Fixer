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
     * @dataProvider provideInvalidConfigurationElementCases
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

    public function provideInvalidConfigurationElementCases(): array
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [false],
            'int' => [1],
            'array' => [[]],
            'float' => [0.1],
            'object' => [new \stdClass()],
            'not-trimmed' => ['  is_string  '],
        ];
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

    public function provideConfigureIncludeSetsCases(): array
    {
        return [
            [['foo', 'bar']],
            [[NativeFunctionInvocationFixer::SET_ALL]],
            [[NativeFunctionInvocationFixer::SET_ALL, 'bar']],
            [
                ['@xxx'],
                InvalidFixerConfigurationException::class,
                '[native_function_invocation] Invalid configuration: Unknown set "@xxx", known sets are "@all", "@internal", "@compiler_optimized".',
            ],
            [
                [' x '],
                InvalidFixerConfigurationException::class,
                '[native_function_invocation] Invalid configuration: Each element must be a non-empty, trimmed string, got "string" instead.',
            ],
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

    public function provideFixWithDefaultConfigurationCases(): array
    {
        return [
            [
                '<?php

\is_string($foo);
',
            ],
            [
                '<?php

\is_string($foo);
',
                '<?php

is_string($foo);
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return \is_string($foo);
    }
}
',
            ],
            [
                '<?php

json_encode($foo);
\strlen($foo);
',
                '<?php

json_encode($foo);
strlen($foo);
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return \IS_STRING($foo);
    }
}
',
                '<?php

class Foo
{
    public function bar($foo)
    {
        return IS_STRING($foo);
    }
}
',
            ],
            'fix multiple calls in single code' => [
                '<?php

json_encode($foo);
\strlen($foo);
\strlen($foo);
',
                '<?php

json_encode($foo);
strlen($foo);
strlen($foo);
',
            ],
            [
                '<?php $name = \get_class($foo, );',
                '<?php $name = get_class($foo, );',
            ],
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

    public function provideFixWithConfiguredExcludeCases(): array
    {
        return [
            [
                '<?php

is_string($foo);
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return is_string($foo);
    }
}
',
            ],
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

    public function provideFixWithNamespaceConfigurationCases(): array
    {
        return [
            [
                '<?php echo count([1]);',
            ],
            [
                '<?php
namespace space1 { ?>
<?php echo \count([2]) ?>
<?php }namespace {echo count([1]);}
',
                '<?php
namespace space1 { ?>
<?php echo count([2]) ?>
<?php }namespace {echo count([1]);}
',
            ],
            [
                '<?php
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
',
                '<?php
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
',
            ],
            [
                '<?php
namespace space11 ?>

    <?php
echo \strlen(__NAMESPACE__);
namespace space2;
echo \strlen(__NAMESPACE__);
',
                '<?php
namespace space11 ?>

    <?php
echo strlen(__NAMESPACE__);
namespace space2;
echo strlen(__NAMESPACE__);
',
            ],
            [
                '<?php namespace PhpCsFixer\Tests\Fixer\Casing;\count([1]);',
                '<?php namespace PhpCsFixer\Tests\Fixer\Casing;count([1]);',
            ],
            [
                '<?php
namespace Space12;

echo \count([1]);

namespace Space2;

echo \count([1]);
?>
',
                '<?php
namespace Space12;

echo count([1]);

namespace Space2;

echo count([1]);
?>
',
            ],
            [
                '<?php namespace {echo strlen("out 2");}',
            ],
            [
                '<?php
namespace space13 {
    echo \strlen("in 1");
}

namespace space2 {
    echo \strlen("in 2");
}

namespace { // global
    echo strlen("global 1");
}
',
                '<?php
namespace space13 {
    echo strlen("in 1");
}

namespace space2 {
    echo strlen("in 2");
}

namespace { // global
    echo strlen("global 1");
}
',
            ],
            [
                '<?php
namespace space1 {
    echo \count([1]);
}
namespace {
    echo \count([1]);
}
',
                '<?php
namespace space1 {
    echo count([1]);
}
namespace {
    echo count([1]);
}
',
                'all',
            ],
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

    public function provideFixWithConfiguredIncludeCases(): iterable
    {
        yield from [
            'include set + 1, exclude 1' => [
                '<?php
                    echo \count([1]);
                    \some_other($a, 3);
                    echo strlen($a);
                    not_me();
                ',
                '<?php
                    echo count([1]);
                    some_other($a, 3);
                    echo strlen($a);
                    not_me();
                ',
                [
                    'include' => [NativeFunctionInvocationFixer::SET_INTERNAL, 'some_other'],
                    'exclude' => ['strlen'],
                ],
            ],
            'include @all' => [
                '<?php
                    echo \count([1]);
                    \some_other($a, 3);
                    echo \strlen($a);
                    \me_as_well();
                ',
                '<?php
                    echo count([1]);
                    some_other($a, 3);
                    echo strlen($a);
                    me_as_well();
                ',
                [
                    'include' => [NativeFunctionInvocationFixer::SET_ALL],
                ],
            ],
            'include @compiler_optimized' => [
                '<?php
                    // do not fix
                    $a = strrev($a);
                    $a .= str_repeat($a, 4);
                    $b = already_prefixed_function();
                    // fix
                    $c = \get_class($d);
                    $e = \intval($f);
                ',
                '<?php
                    // do not fix
                    $a = strrev($a);
                    $a .= str_repeat($a, 4);
                    $b = \already_prefixed_function();
                    // fix
                    $c = get_class($d);
                    $e = intval($f);
                ',
                [
                    'include' => [NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED],
                ],
            ],
            [
                '<?php class Foo {
                        public function & strlen($name) {
                        }
                    }
                ',
            ],
            'scope namespaced and strict enabled' => [
                '<?php
                    $a = not_compiler_optimized_function();
                    $b = intval($c);
                ',
                '<?php
                    $a = \not_compiler_optimized_function();
                    $b = \intval($c);
                ',
                [
                    'scope' => 'namespaced',
                    'strict' => true,
                ],
            ],
            [
                '<?php
                    use function foo\json_decode;
                    json_decode($base);
                ',
                null,
                [
                    'include' => [NativeFunctionInvocationFixer::SET_ALL],
                ],
            ],
        ];

        if (\PHP_VERSION_ID < 80000) {
            yield 'include @compiler_optimized with strict enabled' => [
                '<?php
                        $a = not_compiler_optimized_function();
                        $b =  not_compiler_optimized_function();
                        $c = \intval($d);
                    ',
                '<?php
                        $a = \not_compiler_optimized_function();
                        $b = \ not_compiler_optimized_function();
                        $c = intval($d);
                    ',
                [
                    'include' => [NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED],
                    'strict' => true,
                ],
            ];
        }
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
            '<?php
echo \/**/strlen($a);
echo \ strlen($a);
echo \#
#
strlen($a);
echo \strlen($a);
',
            '<?php
echo \/**/strlen($a);
echo \ strlen($a);
echo \#
#
strlen($a);
echo strlen($a);
'
        );
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

    public function provideFix80Cases(): iterable
    {
        yield 'attribute and strict' => [
            '<?php
                #[\Attribute(\Attribute::TARGET_CLASS)]
                class Foo {}
            ',
            null,
            ['strict' => true],
        ];

        yield 'null safe operator' => ['<?php $x?->count();'];

        yield 'multiple function-calls-like in attribute' => [
            '<?php
                #[Foo(), Bar(), Baz()]
                class Foo {}
            ',
            null,
            ['include' => ['@all']],
        ];
    }
}
