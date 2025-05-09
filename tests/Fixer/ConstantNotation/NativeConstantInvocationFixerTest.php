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

namespace PhpCsFixer\Tests\Fixer\ConstantNotation;

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer>
 *
 * @phpstan-import-type _AutogeneratedInputConfiguration from \PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer
 */
final class NativeConstantInvocationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureRejectsUnknownConfigurationKey(): void
    {
        $key = 'foo';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf('[native_constant_invocation] Invalid configuration: The option "%s" does not exist.', $key));

        $this->fixer->configure([
            $key => 'bar',
        ]);
    }

    /**
     * @dataProvider provideInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidExcludeConfigurationElement($element, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fixer->configure([
            'exclude' => [
                $element,
            ],
        ]);
    }

    /**
     * @dataProvider provideInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidIncludeConfigurationElement($element, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            str_replace('"exclude"', '"include"', $expectedExceptionMessage)
        );

        $this->fixer->configure([
            'include' => [
                $element,
            ],
        ]);
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function provideInvalidConfigurationElementCases(): iterable
    {
        yield 'null' => [
            null,
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "null".',
        ];

        yield 'false' => [
            false,
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "bool".',
        ];

        yield 'true' => [
            true,
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "bool".',
        ];

        yield 'int' => [
            1,
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "int".',
        ];

        yield 'array' => [
            [],
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "array".',
        ];

        yield 'float' => [
            0.1,
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "float".',
        ];

        yield 'object' => [
            new \stdClass(),
            '[native_constant_invocation] Invalid configuration: The option "exclude" with value array is expected to be of type "string[]", but one of the elements is of type "stdClass".',
        ];

        yield 'not-trimmed' => [
            '  M_PI  ',
            '[native_constant_invocation] Invalid configuration: Each element must be a non-empty, trimmed string, got "string" instead.',
        ];
    }

    public function testConfigureResetsExclude(): void
    {
        $this->fixer->configure([
            'exclude' => [
                'M_PI',
            ],
        ]);

        $before = '<?php var_dump(m_pi, M_PI);';
        $after = '<?php var_dump(m_pi, \M_PI);';

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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixWithDefaultConfigurationCases(): iterable
    {
        yield ['<?php var_dump(NULL, FALSE, TRUE, 1);'];

        yield ['<?php echo CUSTOM_DEFINED_CONSTANT_123;'];

        yield ['<?php echo m_pi; // Constant are case sensitive'];

        yield ['<?php namespace M_PI;'];

        yield ['<?php namespace Foo; use M_PI;'];

        yield ['<?php class M_PI {}'];

        yield ['<?php class Foo extends M_PI {}'];

        yield ['<?php class Foo implements M_PI {}'];

        yield ['<?php interface M_PI {};'];

        yield ['<?php trait M_PI {};'];

        yield ['<?php class Foo { const M_PI = 1; }'];

        yield ['<?php class Foo { use M_PI; }'];

        yield ['<?php class Foo { public $M_PI = 1; }'];

        yield ['<?php class Foo { function M_PI($M_PI) {} }'];

        yield ['<?php class Foo { function bar() { $M_PI = M_PI() + self::M_PI(); } }'];

        yield ['<?php class Foo { function bar() { $this->M_PI(self::M_PI); } }'];

        yield ['<?php namespace Foo; use Bar as M_PI;'];

        yield ['<?php echo Foo\M_PI\Bar;'];

        yield ['<?php M_PI::foo();'];

        yield ['<?php function x(M_PI $foo, M_PI &$bar, M_PI ...$baz) {}'];

        yield ['<?php $foo instanceof M_PI;'];

        yield ['<?php class x implements FOO, M_PI, BAZ {}'];

        yield ['<?php class Foo { use Bar, M_PI { Bar::baz insteadof M_PI; } }'];

        yield ['<?php M_PI: goto M_PI;'];

        yield [
            '<?php echo \M_PI;',
            '<?php echo M_PI;',
        ];

        yield [
            '<?php namespace Foo; use M_PI; echo \M_PI;',
            '<?php namespace Foo; use M_PI; echo M_PI;',
        ];

        yield [
            // Here we are just testing the algorithm.
            // A user likely would add this M_PI to its excluded list.
            '<?php namespace M_PI; const M_PI = 1; return \M_PI;',
            '<?php namespace M_PI; const M_PI = 1; return M_PI;',
        ];

        yield [
            '<?php foo(\E_DEPRECATED | \E_USER_DEPRECATED);',
            '<?php foo(E_DEPRECATED | E_USER_DEPRECATED);',
        ];

        yield ['<?php function foo(): M_PI {}'];

        yield ['<?php use X\Y\{FOO, BAR as BAR2, M_PI};'];

        yield [
            '<?php
try {
    foo(\JSON_ERROR_DEPTH|\JSON_PRETTY_PRINT|JOB_QUEUE_PRIORITY_HIGH);
} catch (\Exception | \InvalidArgumentException|\UnexpectedValueException|LogicException $e) {
}
',
            '<?php
try {
    foo(\JSON_ERROR_DEPTH|JSON_PRETTY_PRINT|\JOB_QUEUE_PRIORITY_HIGH);
} catch (\Exception | \InvalidArgumentException|\UnexpectedValueException|LogicException $e) {
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredCustomIncludeCases
     */
    public function testFixWithConfiguredCustomInclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'include' => [
                'FOO_BAR_BAZ',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixWithConfiguredCustomIncludeCases(): iterable
    {
        yield [
            '<?php echo \FOO_BAR_BAZ . \M_PI;',
            '<?php echo FOO_BAR_BAZ . M_PI;',
        ];

        yield [
            '<?php class Foo { public function bar($foo) { return \FOO_BAR_BAZ . \M_PI; } }',
            '<?php class Foo { public function bar($foo) { return FOO_BAR_BAZ . M_PI; } }',
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredOnlyIncludeCases
     */
    public function testFixWithConfiguredOnlyInclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'fix_built_in' => false,
            'include' => [
                'M_PI',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixWithConfiguredOnlyIncludeCases(): iterable
    {
        yield [
            '<?php echo PHP_SAPI . FOO_BAR_BAZ . \M_PI;',
            '<?php echo PHP_SAPI . FOO_BAR_BAZ . M_PI;',
        ];

        yield [
            '<?php class Foo { public function bar($foo) { return PHP_SAPI . FOO_BAR_BAZ . \M_PI; } }',
            '<?php class Foo { public function bar($foo) { return PHP_SAPI . FOO_BAR_BAZ . M_PI; } }',
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredExcludeCases
     */
    public function testFixWithConfiguredExclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'exclude' => [
                'M_PI',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixWithConfiguredExcludeCases(): iterable
    {
        yield [
            '<?php echo \PHP_SAPI . M_PI;',
            '<?php echo PHP_SAPI . M_PI;',
        ];

        yield [
            '<?php class Foo { public function bar($foo) { return \PHP_SAPI . M_PI; } }',
            '<?php class Foo { public function bar($foo) { return PHP_SAPI . M_PI; } }',
        ];
    }

    public function testNullTrueFalseAreCaseInsensitive(): void
    {
        $this->fixer->configure([
            'fix_built_in' => false,
            'include' => [
                'null',
                'false',
                'M_PI',
                'M_pi',
            ],
            'exclude' => [],
        ]);

        $expected = <<<'EOT'
            <?php
            var_dump(
                \null,
                \NULL,
                \Null,
                \nUlL,
                \false,
                \FALSE,
                true,
                TRUE,
                \M_PI,
                \M_pi,
                m_pi,
                m_PI
            );
            EOT;

        $input = <<<'EOT'
            <?php
            var_dump(
                null,
                NULL,
                Null,
                nUlL,
                false,
                FALSE,
                true,
                TRUE,
                M_PI,
                M_pi,
                m_pi,
                m_PI
            );
            EOT;

        $this->doTest($expected, $input);
    }

    public function testDoNotIncludeUserConstantsUnlessExplicitlyListed(): void
    {
        $uniqueConstantName = uniqid(self::class);
        $uniqueConstantName = Preg::replace('/\W+/', '_', $uniqueConstantName);
        $uniqueConstantName = strtoupper($uniqueConstantName);

        $dontFixMe = 'DONTFIXME_'.$uniqueConstantName;
        $fixMe = 'FIXME_'.$uniqueConstantName;

        \define($dontFixMe, 1);
        \define($fixMe, 1);

        $this->fixer->configure([
            'fix_built_in' => true,
            'include' => [
                $fixMe,
            ],
            'exclude' => [],
        ]);

        $expected = <<<EOT
            <?php
            var_dump(
                \\null,
                {$dontFixMe},
                \\{$fixMe}
            );
            EOT;

        $input = <<<EOT
            <?php
            var_dump(
                null,
                {$dontFixMe},
                {$fixMe}
            );
            EOT;

        $this->doTest($expected, $input);
    }

    public function testDoNotFixImportedConstants(): void
    {
        $this->fixer->configure([
            'fix_built_in' => false,
            'include' => [
                'M_PI',
                'M_EULER',
            ],
            'exclude' => [],
        ]);

        $expected = <<<'EOT'
            <?php

            namespace Foo;

            use const M_EULER;

            var_dump(
                null,
                \M_PI,
                M_EULER
            );
            EOT;

        $input = <<<'EOT'
            <?php

            namespace Foo;

            use const M_EULER;

            var_dump(
                null,
                M_PI,
                M_EULER
            );
            EOT;

        $this->doTest($expected, $input);
    }

    public function testFixScopedOnly(): void
    {
        $this->fixer->configure(['scope' => 'namespaced']);

        $expected = <<<'EOT'
            <?php

            namespace space1 {
                echo \PHP_VERSION;
            }
            namespace {
                echo PHP_VERSION;
            }
            EOT;

        $input = <<<'EOT'
            <?php

            namespace space1 {
                echo PHP_VERSION;
            }
            namespace {
                echo PHP_VERSION;
            }
            EOT;

        $this->doTest($expected, $input);
    }

    public function testFixScopedOnlyNoNamespace(): void
    {
        $this->fixer->configure(['scope' => 'namespaced']);

        $expected = <<<'EOT'
            <?php

            echo PHP_VERSION . PHP_EOL;
            EOT;

        $this->doTest($expected);
    }

    public function testFixStrictOption(): void
    {
        $this->fixer->configure(['strict' => true]);
        $this->doTest(
            '<?php
                echo \PHP_VERSION . \PHP_EOL; // built-in constants to have backslash
                echo MY_FRAMEWORK_MAJOR_VERSION . MY_FRAMEWORK_MINOR_VERSION; // non-built-in constants not to have backslash
                echo \Dont\Touch\Namespaced\CONSTANT;
            ',
            '<?php
                echo \PHP_VERSION . PHP_EOL; // built-in constants to have backslash
                echo \MY_FRAMEWORK_MAJOR_VERSION . MY_FRAMEWORK_MINOR_VERSION; // non-built-in constants not to have backslash
                echo \Dont\Touch\Namespaced\CONSTANT;
            '
        );
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
            '<?php
echo \/**/M_PI;
echo \ M_PI;
echo \#
#
M_PI;
echo \M_PI;
',
            '<?php
echo \/**/M_PI;
echo \ M_PI;
echo \#
#
M_PI;
echo M_PI;
'
        );
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected): void
    {
        $this->fixer->configure(['strict' => true]);
        $this->doTest($expected);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFixPhp80Cases(): iterable
    {
        yield [
            '<?php
            try {
            } catch (\Exception) {
            }',
        ];

        yield ['<?php try { foo(); } catch(\InvalidArgumentException|\LogicException $e) {}'];

        yield ['<?php try { foo(); } catch(\InvalidArgumentException|\LogicException) {}'];
    }

    /**
     * @dataProvider provideFixPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testFixPhp81(string $expected): void
    {
        $this->fixer->configure(['strict' => true]);
        $this->doTest($expected);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFixPhp81Cases(): iterable
    {
        yield [
            '<?php enum someEnum: int
                {
                    case E_ALL = 123;
                }',
        ];
    }

    /**
     * @dataProvider provideFixPhp82Cases
     *
     * @requires PHP 8.2
     */
    public function testFixPhp82(string $expected): void
    {
        $this->fixer->configure(['strict' => true]);
        $this->doTest($expected);
    }

    /**
     * @return iterable<int, array{0: string}>
     */
    public static function provideFixPhp82Cases(): iterable
    {
        yield ['<?php class Foo { public (\A&B)|(C&\D)|E\F|\G|(A&H\I)|(A&\J\K) $var; }'];

        yield ['<?php function foo ((\A&B)|(C&\D)|E\F|\G|(A&H\I)|(A&\J\K) $var) {}'];
    }

    /**
     * @dataProvider provideFixPhp83Cases
     *
     * @requires PHP 8.3
     */
    public function testFixPhp83(string $expected, string $input): void
    {
        $this->fixer->configure(['strict' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1: string}>
     */
    public static function provideFixPhp83Cases(): iterable
    {
        yield [
            '<?php class Foo {
                public const string C1 = \PHP_EOL;
                protected const string|int C2 = \PHP_EOL;
                private const string|(A&B) C3 = BAR;
                public const EnumA C4 = EnumA::FOO;
                private const array CONNECTION_TIMEOUT = [\'foo\'];
            }',
            '<?php class Foo {
                public const string C1 = PHP_EOL;
                protected const string|int C2 = \PHP_EOL;
                private const string|(A&B) C3 = \BAR;
                public const EnumA C4 = EnumA::FOO;
                private const array CONNECTION_TIMEOUT = [\'foo\'];
            }',
        ];
    }
}
