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

namespace PhpCsFixer\Tests\RuleSet;

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\RuleSet
 */
final class RuleSetTest extends TestCase
{
    /**
     * @param string     $ruleName
     * @param string     $setName
     * @param array|bool $ruleConfig
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testIfAllRulesInSetsExists($setName, $ruleName, $ruleConfig)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = [];

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        static::assertArrayHasKey($ruleName, $fixers, sprintf('RuleSet "%s" contains unknown rule.', $setName));

        if (true === $ruleConfig) {
            return; // rule doesn't need configuration.
        }

        $fixer = $fixers[$ruleName];
        static::assertInstanceOf(ConfigurableFixerInterface::class, $fixer, sprintf('RuleSet "%s" contains configuration for rule "%s" which cannot be configured.', $setName, $ruleName));

        try {
            $fixer->configure($ruleConfig); // test fixer accepts the configuration
        } catch (InvalidForEnvFixerConfigurationException $exception) {
            // ignore
        }
    }

    /**
     * @param string     $setName
     * @param string     $ruleName
     * @param array|bool $ruleConfig
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testThatDefaultConfigIsNotPassed($setName, $ruleName, $ruleConfig)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $factory->useRuleSet(new RuleSet([$ruleName => true]));

        $fixer = current($factory->getFixers());

        if (!$fixer instanceof ConfigurationDefinitionFixerInterface || \is_bool($ruleConfig)) {
            $this->addToAssertionCount(1);

            return;
        }

        $defaultConfig = [];

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ($option instanceof DeprecatedFixerOptionInterface) {
                continue;
            }

            $defaultConfig[$option->getName()] = $option->getDefault();
        }

        ksort($defaultConfig);
        ksort($ruleConfig);

        static::assertNotSame($defaultConfig, $ruleConfig, sprintf('Rule "%s" (in RuleSet "%s") has default config passed.', $ruleName, $setName));
    }

    /**
     * @param string $ruleName
     * @param string $setName
     *
     * @dataProvider provideAllRulesFromSetsCases
     */
    public function testThatThereIsNoDeprecatedFixerInRuleSet($setName, $ruleName)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $factory->useRuleSet(new RuleSet([$ruleName => true]));

        $fixer = current($factory->getFixers());

        static::assertNotInstanceOf(DeprecatedFixerInterface::class, $fixer, sprintf('RuleSet "%s" contains deprecated rule "%s".', $setName, $ruleName));
    }

    public function provideAllRulesFromSetsCases()
    {
        $cases = [];
        foreach (RuleSets::getSetDefinitionNames() as $setName) {
            $ruleSet = new RuleSet([$setName => true]);
            foreach ($ruleSet->getRules() as $rule => $config) {
                $cases[] = [
                    $setName,
                    $rule,
                    $config,
                ];
            }
        }

        return $cases;
    }

    public function testGetBuildInSetDefinitionNames()
    {
        $setNames = RuleSets::getSetDefinitionNames();

        static::assertIsArray($setNames);
        static::assertNotEmpty($setNames);
    }

    public function testResolveRulesWithInvalidSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Set "@foo" does not exist.');

        new RuleSet(['@foo' => true]);
    }

    public function testResolveRulesWithMissingRuleValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing value for "braces" rule/set.');

        new RuleSet(['braces']);
    }

    public function testResolveRulesWithSet()
    {
        $ruleSet = new RuleSet([
            '@PSR1' => true,
            'braces' => true,
            'encoding' => false,
            'line_ending' => true,
            'strict_comparison' => true,
        ]);

        static::assertSameRules(
            [
                'braces' => true,
                'full_opening_tag' => true,
                'line_ending' => true,
                'strict_comparison' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithNestedSet()
    {
        $ruleSet = new RuleSet([
            '@PSR2' => true,
            'strict_comparison' => true,
        ]);

        static::assertSameRules(
            [
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'constant_case' => true,
                'elseif' => true,
                'encoding' => true,
                'full_opening_tag' => true,
                'function_declaration' => true,
                'indentation_type' => true,
                'line_ending' => true,
                'lowercase_keywords' => true,
                'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
                'no_break_comment' => true,
                'no_closing_tag' => true,
                'no_spaces_after_function_name' => true,
                'no_spaces_inside_parenthesis' => true,
                'no_trailing_whitespace' => true,
                'no_trailing_whitespace_in_comment' => true,
                'single_blank_line_at_eof' => true,
                'single_class_element_per_statement' => ['elements' => ['property']],
                'single_import_per_statement' => true,
                'single_line_after_imports' => true,
                'strict_comparison' => true,
                'switch_case_semicolon_to_colon' => true,
                'switch_case_space' => true,
                'visibility_required' => true,
            ],
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithDisabledSet()
    {
        $ruleSet = new RuleSet([
            '@PSR2' => true,
            '@PSR1' => false,
            'encoding' => true,
        ]);

        static::assertSameRules(
            [
                'blank_line_after_namespace' => true,
                'braces' => true,
                'constant_case' => true,
                'class_definition' => true,
                'elseif' => true,
                'encoding' => true,
                'function_declaration' => true,
                'indentation_type' => true,
                'line_ending' => true,
                'lowercase_keywords' => true,
                'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
                'no_break_comment' => true,
                'no_closing_tag' => true,
                'no_spaces_after_function_name' => true,
                'no_spaces_inside_parenthesis' => true,
                'no_trailing_whitespace' => true,
                'no_trailing_whitespace_in_comment' => true,
                'single_blank_line_at_eof' => true,
                'single_class_element_per_statement' => ['elements' => ['property']],
                'single_import_per_statement' => true,
                'single_line_after_imports' => true,
                'switch_case_semicolon_to_colon' => true,
                'switch_case_space' => true,
                'visibility_required' => true,
            ],
            $ruleSet->getRules()
        );
    }

    /**
     * @param bool $safe
     *
     * @dataProvider provideSafeSetCases
     */
    public function testRiskyRulesInSet(array $set, $safe)
    {
        try {
            $fixers = (new FixerFactory())
                ->registerBuiltInFixers()
                ->useRuleSet(new RuleSet($set))
                ->getFixers()
            ;
        } catch (InvalidForEnvFixerConfigurationException $exception) {
            static::markTestSkipped($exception->getMessage());
        }

        $fixerNames = [];
        foreach ($fixers as $fixer) {
            if ($safe === $fixer->isRisky()) {
                $fixerNames[] = $fixer->getName();
            }
        }

        static::assertCount(
            0,
            $fixerNames,
            sprintf(
                'Set should only contain %s fixers, got: \'%s\'.',
                $safe ? 'safe' : 'risky',
                implode('\', \'', $fixerNames)
            )
        );
    }

    public function provideSafeSetCases()
    {
        $sets = [];

        foreach (RuleSets::getSetDefinitionNames() as $name) {
            $sets[$name] = [
                [$name => true],
                false === strpos($name, ':risky'),
            ];
        }

        $sets['@Symfony:risky_and_@Symfony'] = [
            [
                '@Symfony:risky' => true,
                '@Symfony' => false,
            ],
            false,
        ];

        return $sets;
    }

    public function testInvalidConfigNestedSets()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('#^Nested rule set "@PSR1" configuration must be a boolean\.$#');

        new RuleSet(
            ['@PSR1' => ['@PSR2' => 'no']]
        );
    }

    public function testGetMissingRuleConfiguration()
    {
        $ruleSet = new RuleSet();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Rule "_not_exists" is not in the set\.$#');

        $ruleSet->getRuleConfiguration('_not_exists');
    }

    public function testDuplicateRuleConfigurationInSetDefinitions()
    {
        $resolvedSets = [];
        $setDefinitions = RuleSets::getSetDefinitions();

        foreach ($setDefinitions as $setName => $setDefinition) {
            $resolvedSets[$setName] = ['rules' => [], 'sets' => []];

            foreach ($setDefinition->getRules() as $name => $value) {
                if ('@' === $name[0]) {
                    $resolvedSets[$setName]['sets'][$name] = $this->expendSet($setDefinitions, $resolvedSets, $name, $value);
                } else {
                    $resolvedSets[$setName]['rules'][$name] = $value;
                }
            }
        }

        $duplicates = [];

        foreach ($resolvedSets as $name => $resolvedSet) {
            foreach ($resolvedSet['rules'] as $ruleName => $config) {
                if (\count($resolvedSet['sets']) < 1) {
                    continue;
                }

                $setDuplicates = $this->findInSets($resolvedSet['sets'], $ruleName, $config);

                if (\count($setDuplicates) > 0) {
                    if (!isset($duplicates[$name])) {
                        $duplicates[$name] = [];
                    }

                    $duplicates[$name][$ruleName] = $setDuplicates;
                }
            }
        }

        if (\count($duplicates) > 0) {
            $message = '';

            foreach ($duplicates as $setName => $r) {
                $message .= sprintf("\n\"%s\" defines rules the same as it extends from:", $setName);

                foreach ($duplicates[$setName] as $ruleName => $otherSets) {
                    $message .= sprintf("\n- \"%s\" is also in \"%s\"", $ruleName, implode(', ', $otherSets));
                }
            }

            static::fail($message);
        } else {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @param string $version
     *
     * @dataProvider providePhpUnitTargetVersionHasSetCases
     */
    public function testPhpUnitTargetVersionHasSet($version)
    {
        static::assertContains(
            sprintf('@PHPUnit%sMigration:risky', str_replace('.', '', $version)),
            RuleSets::getSetDefinitionNames(),
            sprintf('PHPUnit target version %s is missing its set in %s.', $version, RuleSet::class)
        );
    }

    public static function providePhpUnitTargetVersionHasSetCases()
    {
        foreach ((new \ReflectionClass(PhpUnitTargetVersion::class))->getConstants() as $constant) {
            if ('newest' === $constant) {
                continue;
            }
            yield [$constant];
        }
    }

    /**
     * @group legacy
     * @expectedDeprecation PhpCsFixer\RuleSet\RuleSet::create is deprecated and will be removed in 3.0, use the constructor.
     */
    public function testCreate()
    {
        $ruleSet = RuleSet::create();

        static::assertInstanceOf(RuleSet::class, $ruleSet);
    }

    /**
     * @group legacy
     * @expectedDeprecation PhpCsFixer\RuleSet\RuleSet::getSetDefinitionNames is deprecated and will be removed in 3.0, use PhpCsFixer\RuleSet\RuleSets::getSetDefinitionNames.
     */
    public function testGetSetDefinitionNames()
    {
        $ruleSet = new RuleSet([]);
        $ruleSet->getSetDefinitionNames();
    }

    private function findInSets(array $sets, $ruleName, $config)
    {
        $duplicates = [];

        foreach ($sets as $setName => $setRules) {
            if (\array_key_exists($ruleName, $setRules['rules'])) {
                if ($config === $setRules['rules'][$ruleName]) {
                    $duplicates[] = $setName;
                }

                break; // do not check below, config for the rule has been changed
            }

            if (isset($setRules['sets']) && \count($setRules['sets']) > 0) {
                $subSetDuplicates = $this->findInSets($setRules['sets'], $ruleName, $config);

                if (\count($subSetDuplicates) > 0) {
                    $duplicates = array_merge($duplicates, $subSetDuplicates);
                }
            }
        }

        return $duplicates;
    }

    private function expendSet($setDefinitions, $resolvedSets, $setName, $setValue)
    {
        $rules = $setDefinitions[$setName]->getRules();

        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                $resolvedSets[$setName]['sets'][$name] = $this->expendSet($setDefinitions, $resolvedSets, $name, $setValue);
            } elseif (!$setValue) {
                $resolvedSets[$setName]['rules'][$name] = false;
            } else {
                $resolvedSets[$setName]['rules'][$name] = $value;
            }
        }

        return $resolvedSets[$setName];
    }

    private static function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        static::assertSame($expected, $actual, $message);
    }
}
