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

namespace PhpCsFixer\Tests;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\InternalFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerFactory
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerFactoryTest extends TestCase
{
    public function testInterfaceIsFluent(): void
    {
        $factory = new FixerFactory();

        $testInstance = $factory->registerBuiltInFixers();
        self::assertSame($factory, $testInstance);

        $testInstance = $factory->registerCustomFixers(
            [$this->createFixerDouble('Foo/f1'), $this->createFixerDouble('Foo/f2')],
        );

        self::assertSame($factory, $testInstance);

        $testInstance = $factory->registerFixer(
            $this->createFixerDouble('f3'),
            false,
        );

        self::assertSame($factory, $testInstance);

        $ruleSet = new class([]) implements RuleSetInterface {
            /** @var array<string, array<string, mixed>|true> */
            private array $set;

            /** @param array<string, array<string, mixed>|true> $set */
            public function __construct(array $set = [])
            {
                $this->set = $set;
            }

            public function getRuleConfiguration(string $rule): ?array
            {
                throw new \LogicException('Not implemented.');
            }

            public function getRules(): array
            {
                return $this->set;
            }

            public function hasRule(string $rule): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $testInstance = $factory->useRuleSet(
            $ruleSet,
        );

        self::assertSame($factory, $testInstance);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerBuiltInFixers
     */
    public function testRegisterBuiltInFixers(): void
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixerClasses = array_filter(
            get_declared_classes(),
            static function (string $className): bool {
                $class = new \ReflectionClass($className);

                return !$class->isAbstract()
                    && !$class->isAnonymous()
                    && $class->implementsInterface(FixerInterface::class)
                    && !$class->implementsInterface(InternalFixerInterface::class)
                    && str_starts_with($class->getNamespaceName(), 'PhpCsFixer\Fixer\\');
            },
        );

        sort($fixerClasses);

        $fixers = array_map(
            static fn (FixerInterface $fixer): string => \get_class($fixer),
            $factory->getFixers(),
        );

        sort($fixers);

        self::assertSame($fixerClasses, $fixers);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     */
    public function testThatFixersAreSorted(): void
    {
        $factory = new FixerFactory();
        $fxs = [
            $this->createFixerDouble('f1', 0),
            $this->createFixerDouble('f2', -10),
            $this->createFixerDouble('f3', 10),
            $this->createFixerDouble('f4', -10),
        ];

        foreach ($fxs as $fx) {
            $factory->registerFixer($fx, false);
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        self::assertSame([$fxs[2], $fxs[0]], \array_slice($factory->getFixers(), 0, 2));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     * @covers \PhpCsFixer\FixerFactory::registerCustomFixers
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testThatCanRegisterAndGetFixers(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');

        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers([$f2, $f3]);

        self::assertTrue(\in_array($f1, $factory->getFixers(), true));
        self::assertTrue(\in_array($f2, $factory->getFixers(), true));
        self::assertTrue(\in_array($f3, $factory->getFixers(), true));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testRegisterFixerWithOccupiedName(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fixer named "non_unique_name" is already registered.');

        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('non_unique_name');
        $f2 = $this->createFixerDouble('non_unique_name');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSet(): void
    {
        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet([]))
        ;

        self::assertCount(0, $factory->getFixers());

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(['strict_comparison' => true, 'blank_line_before_statement' => false]))
        ;

        $fixers = $factory->getFixers();
        self::assertCount(1, $fixers);
        self::assertSame('strict_comparison', $fixers[0]->getName());
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSetWithNonExistingRule(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Rule "non_existing_rule" does not exist.');

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(['non_existing_rule' => true]))
        ;

        $factory->getFixers();
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSetWithInvalidConfigForRule(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('Configuration must be an array and may not be empty.');

        $testRuleSet = new class implements RuleSetInterface {
            public function __construct(array $set = [])
            {
                if ([] !== $set) {
                    throw new \RuntimeException('Set is not used in test.');
                }
            }

            /**
             * @return array<string, mixed>
             */
            public function getRuleConfiguration(string $rule): ?array
            {
                if (!$this->hasRule($rule)) {
                    throw new \InvalidArgumentException(\sprintf('Rule "%s" is not in the set.', $rule));
                }

                // @phpstan-ignore-next-line offsetAccess.notFound The offset existence was check in the `if` above
                if (true === $this->getRules()[$rule]) {
                    return null;
                }

                return $this->getRules()[$rule];
            }

            public function getRules(): array
            {
                return ['header_comment' => []];
            }

            public function hasRule(string $rule): bool
            {
                return isset($this->getRules()[$rule]);
            }
        };

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet($testRuleSet)
        ;

        $factory->getFixers();
    }

    public function testHasRule(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');
        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers([$f2, $f3]);

        self::assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        self::assertTrue($factory->hasRule('Foo/f2'), 'Should have f2 fixer');
        self::assertTrue($factory->hasRule('Foo/f3'), 'Should have f3 fixer');
        self::assertFalse($factory->hasRule('dummy'), 'Should not have dummy fixer');
    }

    public function testHasRuleWithChangedRuleSet(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('f2');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);

        self::assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        self::assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');

        $factory->useRuleSet(new RuleSet(['f2' => true]));
        self::assertFalse($factory->hasRule('f1'), 'Should not have f1 fixer');
        self::assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');
    }

    /**
     * @param array<string, array<string, mixed>|bool> $ruleSet
     *
     * @dataProvider provideConflictingFixersCases
     */
    public function testConflictingFixers(array $ruleSet): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('#^Rule contains conflicting fixers:\n#');

        (new FixerFactory())
            ->registerBuiltInFixers()->useRuleSet(new RuleSet($ruleSet))
        ;
    }

    /**
     * @return iterable<int, array{array<string, array<string, mixed>|true>}>
     */
    public static function provideConflictingFixersCases(): iterable
    {
        yield [['no_blank_lines_before_namespace' => true, 'single_blank_line_before_namespace' => true]];

        yield [['single_blank_line_before_namespace' => true, 'no_blank_lines_before_namespace' => true]];
    }

    public function testNoDoubleConflictReporting(): void
    {
        $factory = new FixerFactory();
        self::assertSame(
            'Rule contains conflicting fixers:
- "a" with "b"
- "c" with "d", "e" and "f"
- "d" with "g" and "h"
- "e" with "a"',
            \Closure::bind(static fn (FixerFactory $factory): string => $factory->generateConflictMessage([
                'a' => ['b'],
                'b' => ['a'],
                'c' => ['d', 'e', 'f'],
                'd' => ['c', 'g', 'h'],
                'e' => ['a'],
            ]), null, FixerFactory::class)($factory),
        );
    }

    public function testSetWhitespacesConfig(): void
    {
        $factory = new FixerFactory();
        $config = new WhitespacesFixerConfig();

        $fixer = new class($config) implements WhitespacesAwareFixerInterface {
            private WhitespacesFixerConfig $config;

            public function __construct(WhitespacesFixerConfig $config)
            {
                $this->config = $config;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return 'foo';
            }

            public function getPriority(): int
            {
                throw new \LogicException('Not implemented.');
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function setWhitespacesConfig(WhitespacesFixerConfig $config): void
            {
                TestCase::assertSame($this->config, $config);
            }
        };

        $factory->registerFixer($fixer, false);
        $factory->setWhitespacesConfig($config);
    }

    public function testRegisterFixerInvalidName(): void
    {
        $factory = new FixerFactory();

        $fixer = $this->createFixerDouble('0');

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fixer named "0" has invalid name.');

        $factory->registerFixer($fixer, false);
    }

    public function testConfigureNonConfigurableFixer(): void
    {
        $factory = new FixerFactory();

        $fixer = $this->createFixerDouble('non_configurable');
        $factory->registerFixer($fixer, false);

        $this->expectException(InvalidFixerConfigurationException::class);

        $this->expectExceptionMessage('[non_configurable] Is not configurable.');

        $factory->useRuleSet(new RuleSet([
            'non_configurable' => ['bar' => 'baz'],
        ]));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideConfigureFixerWithNonArrayCases
     */
    public function testConfigureFixerWithNonArray($value): void
    {
        $factory = new FixerFactory();

        $fixer = new class implements ConfigurableFixerInterface {
            public function configure(array $configuration): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getConfigurationDefinition(): FixerConfigurationResolverInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return 'foo';
            }

            public function getPriority(): int
            {
                throw new \LogicException('Not implemented.');
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $factory->registerFixer($fixer, false);

        $this->expectException(InvalidFixerConfigurationException::class);

        $this->expectExceptionMessage(
            '[foo] Rule must be enabled (true), disabled (false) or configured (non-empty, assoc array). Other values are not allowed.',
        );

        $factory->useRuleSet(new RuleSet([
            'foo' => $value,
        ]));
    }

    /**
     * @return iterable<int, array{float|int|\stdClass|string}>
     */
    public static function provideConfigureFixerWithNonArrayCases(): iterable
    {
        yield ['bar'];

        yield [new \stdClass()];

        yield [5];

        yield [5.5];
    }

    public function testConfigurableFixerIsConfigured(): void
    {
        $fixer = new class implements ConfigurableFixerInterface {
            public function configure(array $configuration): void
            {
                TestCase::assertSame(['bar' => 'baz'], $configuration);
            }

            public function getConfigurationDefinition(): FixerConfigurationResolverInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return 'foo';
            }

            public function getPriority(): int
            {
                throw new \LogicException('Not implemented.');
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $factory = new FixerFactory();
        $factory->registerFixer($fixer, false);
        $factory->useRuleSet(new RuleSet([
            'foo' => ['bar' => 'baz'],
        ]));
    }

    private function createFixerDouble(string $name, int $priority = 0): FixerInterface
    {
        return new class($name, $priority) implements FixerInterface {
            private string $name;
            private int $priority;

            public function __construct(string $name, int $priority)
            {
                $this->name = $name;
                $this->priority = $priority;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
