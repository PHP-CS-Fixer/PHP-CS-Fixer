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

namespace PhpCsFixer\Tests\RuleSet;

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\Test\TestCaseUtils;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @group legacy
 *
 * @covers \PhpCsFixer\RuleSet\RuleSet
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RuleSetTest extends TestCase
{
    /**
     * Options for which order of array elements matters.
     *
     * @var list<string>
     */
    private const ORDER_MATTERS = [
        'ordered_imports.imports_order',
        'phpdoc_order.order',
    ];

    /**
     * @param array<string, mixed>|true $ruleConfig
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testIfAllRulesInSetsExists(string $setName, string $ruleName, $ruleConfig): void
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = [];

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        self::assertArrayHasKey($ruleName, $fixers, \sprintf('RuleSet "%s" contains unknown rule.', $setName));

        if (true === $ruleConfig) {
            return; // rule doesn't need configuration.
        }

        \assert(\array_key_exists($ruleName, $fixers));
        $fixer = $fixers[$ruleName];
        self::assertInstanceOf(ConfigurableFixerInterface::class, $fixer, \sprintf('RuleSet "%s" contains configuration for rule "%s" which cannot be configured.', $setName, $ruleName));

        try {
            $fixer->configure($ruleConfig); // test fixer accepts the configuration
        } catch (InvalidForEnvFixerConfigurationException $exception) {
            // ignore
        }
    }

    /**
     * @param array<string, mixed>|true $ruleConfig
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testThatDefaultConfigIsNotPassed(string $setName, string $ruleName, $ruleConfig): void
    {
        $fixer = TestCaseUtils::getFixerByName($ruleName);

        if (!$fixer instanceof ConfigurableFixerInterface || \is_bool($ruleConfig)) {
            $this->expectNotToPerformAssertions();

            return;
        }

        if (\in_array($ruleName, [
            'type_declaration_spaces', // @TODO v4: default value for this rule will changed, remove it when they are changed
        ], true)) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $defaultConfig = [];

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ($option instanceof DeprecatedFixerOptionInterface) {
                continue;
            }

            $defaultConfig[$option->getName()] = $option->getDefault();
        }

        self::assertNotSame(
            $this->sortNestedArray($defaultConfig, $ruleName),
            $this->sortNestedArray($ruleConfig, $ruleName),
            \sprintf('Rule "%s" (in RuleSet "%s") has default config passed.', $ruleName, $setName)
        );
    }

    /**
     * @param array<string, mixed>|true $ruleConfig
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testThatThereIsNoDeprecatedFixerInRuleSet(string $setName, string $ruleName, $ruleConfig): void
    {
        $fixer = TestCaseUtils::getFixerByName($ruleName);

        self::assertNotInstanceOf(DeprecatedFixerInterface::class, $fixer, \sprintf('RuleSet "%s" contains deprecated rule "%s".', $setName, $ruleName));
    }

    /**
     * @return iterable<string, array{string, string, array<string, mixed>|true}>
     */
    public static function provideAllRulesFromSetsCases(): iterable
    {
        foreach (RuleSets::getSetDefinitionNames() as $setName) {
            $ruleSet = new RuleSet([$setName => true]);

            foreach ($ruleSet->getRules() as $rule => $config) {
                yield $setName.':'.$rule => [
                    $setName,
                    $rule,
                    $config,
                ];
            }
        }
    }

    public function testGetBuildInSetDefinitionNames(): void
    {
        $setNames = RuleSets::getSetDefinitionNames();

        self::assertNotEmpty($setNames);
    }

    public function testResolveRulesWithInvalidSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Set "@foo" does not exist.');

        new RuleSet(['@foo' => true]);
    }

    public function testResolveRulesWithMissingRuleValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing value for "braces" rule/set.');

        // @phpstan-ignore-next-line
        new RuleSet(['braces']);
    }

    public function testResolveRulesWithSet(): void
    {
        $ruleSet = new RuleSet([
            '@PSR1' => true,
            'braces' => true,
            'encoding' => false,
            'line_ending' => true,
            'strict_comparison' => true,
        ]);

        self::assertSameRules(
            [
                'braces' => true,
                'full_opening_tag' => true,
                'line_ending' => true,
                'strict_comparison' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithNestedSet(): void
    {
        $ruleSet = new RuleSet([
            '@PHP70Migration' => true,
            'strict_comparison' => true,
        ]);

        self::assertSameRules(
            [
                'array_syntax' => true,
                'strict_comparison' => true,
                'ternary_to_null_coalescing' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithDisabledSet(): void
    {
        $ruleSet = new RuleSet([
            '@PHP70Migration' => true,
            '@PHP54Migration' => false,
            'strict_comparison' => true,
        ]);

        self::assertSameRules(
            [
                'strict_comparison' => true,
                'ternary_to_null_coalescing' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithRuleFromSetDisabledInRootViaLegacyName(): void
    {
        $ruleSet = new RuleSet([
            '@PHP7x1Migration' => true,
            'visibility_required' => false, // this is old name of `modifier_keywords`
        ]);

        self::assertSameRules(
            [
                'array_syntax' => true,
                'list_syntax' => true,
                // 'modifier_keywords' => false, // rule disabled via `visibility_required: false`
                'ternary_to_null_coalescing' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithRuleDisabledInRootViaLegacyNameEnabledViaSet(): void
    {
        $ruleSet = new RuleSet([
            'visibility_required' => false, // this is old name of `modifier_keywords`
            '@PHP7x1Migration' => true,
        ]);

        self::assertSameRules(
            [
                'array_syntax' => true,
                'list_syntax' => true,
                'modifier_keywords' => true, // rule initially disabled via `visibility_required: false`
                'ternary_to_null_coalescing' => true,
            ],
            $ruleSet->getRules()
        );
    }

    /**
     * @param array<string, array<string, mixed>|bool> $set
     *
     * @dataProvider provideRiskyRulesInSetCases
     */
    public function testRiskyRulesInSet(array $set, bool $safe): void
    {
        /** @TODO 4.0 Remove this expectations */
        $expectedDeprecations = [
            '@PER' => 'Rule set "@PER" is deprecated. Use "@PER-CS" instead.',
            '@PER:risky' => 'Rule set "@PER:risky" is deprecated. Use "@PER-CS:risky" instead.',
        ];
        if (\array_key_exists(array_key_first($set), $expectedDeprecations)) {
            $this->expectDeprecation($expectedDeprecations[array_key_first($set)]);
        }

        try {
            $fixers = (new FixerFactory())
                ->registerBuiltInFixers()
                ->useRuleSet(new RuleSet($set))
                ->getFixers()
            ;
        } catch (InvalidForEnvFixerConfigurationException $exception) {
            self::markTestSkipped($exception->getMessage());
        }

        $fixerNames = [];
        foreach ($fixers as $fixer) {
            if ($safe === $fixer->isRisky()) {
                $fixerNames[] = $fixer->getName();
            }
        }

        self::assertCount(
            0,
            $fixerNames,
            \sprintf(
                'Set should only contain %s fixers, got: \'%s\'.',
                $safe ? 'safe' : 'risky',
                implode('\', \'', $fixerNames)
            )
        );
    }

    /**
     * @return iterable<string, array{array<string, array<string, mixed>|bool>, bool}>
     */
    public static function provideRiskyRulesInSetCases(): iterable
    {
        foreach (RuleSets::getSetDefinitionNames() as $name) {
            yield $name => [
                [$name => true],
                !str_contains($name, ':risky'),
            ];
        }

        yield '@Symfony:risky_and_@Symfony' => [
            [
                '@Symfony:risky' => true,
                '@Symfony' => false,
            ],
            false,
        ];
    }

    public function testInvalidConfigNestedSets(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('#^Nested rule set "@PSR1" configuration must be a boolean\.$#');

        new RuleSet(
            ['@PSR1' => ['@PSR2' => 'no']]
        );
    }

    public function testGetMissingRuleConfiguration(): void
    {
        $ruleSet = new RuleSet();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Rule "_not_exists" is not in the set\.$#');

        $ruleSet->getRuleConfiguration('_not_exists');
    }

    /**
     * @dataProvider provideDuplicateRuleConfigurationInSetDefinitionsCases
     */
    public function testDuplicateRuleConfigurationInSetDefinitions(RuleSetDefinitionInterface $set): void
    {
        $rules = [];
        $setRules = [];

        foreach ($set->getRules() as $ruleName => $ruleConfig) {
            if (str_starts_with($ruleName, '@')) {
                if (true !== $ruleConfig && false !== $ruleConfig) {
                    throw new \LogicException('Disallowed configuration for RuleSet.');
                }
                $setRules = array_merge($setRules, $this->resolveSet($ruleName, $ruleConfig));
            } else {
                $rules[$ruleName] = $ruleConfig;
            }
        }

        $duplicates = [];

        foreach ($rules as $ruleName => $ruleConfig) {
            if (!\array_key_exists($ruleName, $setRules)) {
                continue;
            }

            if ($ruleConfig !== $setRules[$ruleName]) {
                continue;
            }

            $duplicates[] = $ruleName;
        }

        if (0 === \count($duplicates)) {
            $this->addToAssertionCount(1);

            return;
        }

        self::fail(\sprintf(
            '"%s" defines rules the same as it extends from: %s',
            $set->getName(),
            implode(', ', $duplicates),
        ));
    }

    /**
     * @return iterable<string, array{RuleSetDefinitionInterface}>
     */
    public static function provideDuplicateRuleConfigurationInSetDefinitionsCases(): iterable
    {
        foreach (RuleSets::getBuiltInSetDefinitions() as $name => $set) {
            yield $name => [$set];
        }
    }

    /**
     * @dataProvider providePhpUnitTargetVersionHasSetCases
     */
    public function testPhpUnitTargetVersionHasSet(string $version): void
    {
        self::assertContains(
            \sprintf('@PHPUnit%sMigration:risky', str_replace('.', '', $version)),
            RuleSets::getSetDefinitionNames(),
            \sprintf('PHPUnit target version %s is missing its set in %s.', $version, RuleSet::class)
        );
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function providePhpUnitTargetVersionHasSetCases(): iterable
    {
        foreach ((new \ReflectionClass(PhpUnitTargetVersion::class))->getConstants() as $constant) {
            if ('newest' === $constant) {
                continue;
            }

            yield [$constant];
        }
    }

    public function testEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule/set name must not be empty.');

        new RuleSet(['' => true]);
    }

    public function testInvalidConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('[@Symfony:risky] Set must be enabled (true) or disabled (false). Other values are not allowed. To disable the set, use "FALSE" instead of "NULL".');

        // @phpstan-ignore-next-line
        new RuleSet(['@Symfony:risky' => null]);
    }

    /**
     * @param array<array-key, mixed> $array
     *
     * @return array<array-key, mixed> $array
     */
    private function sortNestedArray(array $array, string $ruleName): array
    {
        $this->doSort($array, $ruleName);

        return $array;
    }

    /**
     * Sorts an array of fixer definition recursively.
     *
     * Sometimes keys are all string, sometimes they are integers - we need to account for that.
     *
     * @param array<array-key, mixed> $data
     */
    private function doSort(array &$data, string $path): void
    {
        // if order matters do not sort!
        if (\in_array($path, self::ORDER_MATTERS, true)) {
            return;
        }

        $keys = array_keys($data);

        if ($this->allInteger($keys)) {
            sort($data);
        } else {
            ksort($data);
        }

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $this->doSort(
                    $data[$key],
                    $path.('' !== $path ? '.' : '').$key
                );
            }
        }
    }

    /**
     * @param array<int|string, mixed> $values
     */
    private function allInteger(array $values): bool
    {
        foreach ($values as $value) {
            if (!\is_int($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, array<string, mixed>|bool>
     */
    private function resolveSet(string $setName, bool $setValue): array
    {
        $rules = RuleSets::getSetDefinition($setName)->getRules();

        foreach ($rules as $name => $value) {
            if (str_starts_with($name, '@')) {
                $set = $this->resolveSet($name, $setValue);
                unset($rules[$name]);
                $rules = array_merge($rules, $set);
            } elseif (!$setValue) {
                $rules[$name] = false;
            } else {
                $rules[$name] = $value;
            }
        }

        return $rules;
    }

    /**
     * @param array<string, array<string, mixed>|bool> $expected
     * @param array<string, array<string, mixed>|bool> $actual
     */
    private static function assertSameRules(array $expected, array $actual): void
    {
        ksort($expected);
        ksort($actual);

        self::assertSame($expected, $actual);
    }
}
