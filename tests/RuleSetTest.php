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

namespace PhpCsFixer\Tests;

use PhpCsFixer\AccessibleObject\AccessibleObject;
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet
 */
final class RuleSetTest extends TestCase
{
    public function testCreate()
    {
        $ruleSet = new RuleSet();

        static::assertInstanceOf(\PhpCsFixer\RuleSet::class, $ruleSet);
    }

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

        if (!$fixer instanceof ConfigurableFixerInterface) {
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
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            foreach (RuleSet::create([$setName => true])->getRules() as $rule => $config) {
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
        $setNames = RuleSet::create()->getSetDefinitionNames();

        static::assertInternalType('array', $setNames);
        static::assertNotEmpty($setNames);
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param mixed $setName
     */
    public function testBuildInSetDefinitionNames($setName)
    {
        static::assertInternalType('string', $setName);
        static::assertSame('@', substr($setName, 0, 1));
    }

    public function testResolveRulesWithInvalidSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Set "@foo" does not exist.');

        RuleSet::create([
            '@foo' => true,
        ]);
    }

    public function testResolveRulesWithMissingRuleValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing value for "braces" rule/set.');

        RuleSet::create([
            'braces',
        ]);
    }

    public function testResolveRulesWithSet()
    {
        $ruleSet = RuleSet::create([
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
        $ruleSet = RuleSet::create([
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
                'method_argument_space' => [
                    'on_multiline' => 'ensure_fully_multiline',
                ],
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
        $ruleSet = RuleSet::create([
            '@PSR2' => true,
            '@PSR1' => false,
            'encoding' => true,
        ]);

        static::assertSameRules(
            [
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'constant_case' => true,
                'elseif' => true,
                'encoding' => true,
                'function_declaration' => true,
                'indentation_type' => true,
                'line_ending' => true,
                'lowercase_keywords' => true,
                'method_argument_space' => [
                    'on_multiline' => 'ensure_fully_multiline',
                ],
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
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param string $setDefinitionName
     */
    public function testSetDefinitionsAreSorted($setDefinitionName)
    {
        $reflection = new AccessibleObject(new RuleSet());

        $setDefinition = $reflection->getSetDefinition($setDefinitionName);

        $sortedSetDefinition = $setDefinition;

        $this->sort($sortedSetDefinition);

        static::assertSame($sortedSetDefinition, $setDefinition, sprintf(
            'Failed to assert that the set definition for "%s" is sorted by key',
            $setDefinitionName
        ));
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param string $setDefinitionName
     */
    public function testSetDefinitionIsNotEmpty($setDefinitionName)
    {
        $reflection = new AccessibleObject(new RuleSet());

        static::assertNotEmpty($reflection->getSetDefinition($setDefinitionName), sprintf(
            'Failed to assert that the set definition for "%s" is not empty',
            $setDefinitionName
        ));
    }

    /**
     * @return array
     */
    public function provideSetDefinitionNameCases()
    {
        $setDefinitionNames = RuleSet::create()->getSetDefinitionNames();

        return array_map(static function ($setDefinitionName) {
            return [$setDefinitionName];
        }, $setDefinitionNames);
    }

    /**
     * @param bool $safe
     *
     * @dataProvider provideSafeSetCases
     */
    public function testRiskyRulesInSet(array $set, $safe)
    {
        try {
            $fixers = FixerFactory::create()
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

        $ruleSet = new RuleSet();

        foreach ($ruleSet->getSetDefinitionNames() as $name) {
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
        $this->expectExceptionMessageRegExp('#^Nested rule set "@PSR1" configuration must be a boolean\.$#');

        new RuleSet(
            ['@PSR1' => ['@PSR2' => 'no']]
        );
    }

    public function testGetSetDefinitionNames()
    {
        $ruleSet = $this->createRuleSetToTestWith([]);

        static::assertSame(
            array_keys(self::getRuleSetDefinitionsToTestWith()),
            $ruleSet->getSetDefinitionNames()
        );
    }

    /**
     * @dataProvider provideResolveRulesCases
     */
    public function testResolveRules(array $expected, array $rules)
    {
        $ruleSet = $this->createRuleSetToTestWith($rules);

        static::assertSameRules($expected, $ruleSet->getRules());
    }

    public function provideResolveRulesCases()
    {
        return [
            '@Foo + C\' -D' => [
                ['A' => true, 'B' => true, 'C' => 56],
                ['@Foo' => true, 'C' => 56, 'D' => false],
            ],
            '@Foo + @Bar' => [
                ['A' => true, 'B' => true, 'D' => 34, 'E' => true],
                ['@Foo' => true, '@Bar' => true],
            ],
            '@Foo - @Bar' => [
                ['B' => true],
                ['@Foo' => true, '@Bar' => false],
            ],
            '@A - @E (set in set)' => [
                ['AA' => true], // 'AB' => false, 'AC' => false
                ['@A' => true, '@E' => false],
            ],
            '@A + @E (set in set)' => [
                ['AA' => true, 'AB' => '_AB', 'AC' => 'b', 'Z' => true],
                ['@A' => true, '@E' => true],
            ],
            '@E + @A (set in set) + rule override' => [
                ['AC' => 'd', 'AB' => true, 'Z' => true, 'AA' => true],
                ['@E' => true, '@A' => true, 'AC' => 'd'],
            ],
            'nest single set' => [
                ['AC' => 'b', 'AB' => '_AB', 'Z' => 'E'],
                ['@F' => true],
            ],
            'Set reconfigure rule in other set, reconfigure rule.' => [
                [
                    'AA' => true,
                    'AB' => true,
                    'AC' => 'abc',
                ],
                [
                    '@A' => true,
                    '@D' => true,
                    'AC' => 'abc',
                ],
            ],
            'Set reconfigure rule in other set.' => [
                [
                    'AA' => true,
                    'AB' => true,
                    'AC' => 'b',
                ],
                [
                    '@A' => true,
                    '@D' => true,
                ],
            ],
            'Set minus two sets minus rule' => [
                [
                    'AB' => true,
                ],
                [
                    '@A' => true,
                    '@B' => false,
                    '@C' => false,
                    'AC' => false,
                ],
            ],
            'Set minus two sets' => [
                [
                    'AB' => true,
                    'AC' => 'a',
                ],
                [
                    '@A' => true,
                    '@B' => false,
                    '@C' => false,
                ],
            ],
            'Set minus rule test.' => [
                [
                    'AA' => true,
                    'AC' => 'a',
                ],
                [
                    '@A' => true,
                    'AB' => false,
                ],
            ],
            'Set minus set test.' => [
                [
                    'AB' => true,
                    'AC' => 'a',
                ],
                [
                    '@A' => true,
                    '@B' => false,
                ],
            ],
            'Set to rules test.' => [
                [
                    'AA' => true,
                    'AB' => true,
                    'AC' => 'a',
                ],
                [
                    '@A' => true,
                ],
            ],
            '@A - @C' => [
                [
                    'AB' => true,
                    'AC' => 'a',
                ],
                [
                    '@A' => true,
                    '@C' => false,
                ],
            ],
            '@A - @D' => [
                [
                    'AA' => true,
                    'AB' => true,
                ],
                [
                    '@A' => true,
                    '@D' => false,
                ],
            ],
        ];
    }

    public function testGetMissingRuleConfiguration()
    {
        $ruleSet = new RuleSet();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Rule "_not_exists" is not in the set\.$#');

        $ruleSet->getRuleConfiguration('_not_exists');
    }

    /**
     * @dataProvider providePHPUnitMigrationSetDefinitionNameCases
     *
     * @param string $setName
     */
    public function testPHPUnitMigrationTargetVersions($setName)
    {
        $ruleSet = new RuleSet([$setName => true]);

        foreach ($ruleSet->getRules() as $ruleName => $ruleConfig) {
            $targetVersion = true === $ruleConfig ? $this->getDefaultPHPUnitTargetOfRule($ruleName) : $ruleConfig['target'];

            static::assertPHPUnitVersionIsLargestAllowed($setName, $ruleName, $targetVersion);
        }
    }

    /**
     * @return string[][]
     */
    public function providePHPUnitMigrationSetDefinitionNameCases()
    {
        $setDefinitionNames = RuleSet::create()->getSetDefinitionNames();

        $setDefinitionPHPUnitMigrationNames = array_filter($setDefinitionNames, function ($setDefinitionName) {
            return 1 === preg_match('/^@PHPUnit\d{2}Migration:risky$/', $setDefinitionName);
        });

        return array_map(static function ($setDefinitionName) {
            return [$setDefinitionName];
        }, $setDefinitionPHPUnitMigrationNames);
    }

    private static function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        static::assertSame($expected, $actual, $message);
    }

    /**
     * Sorts an array of rule set definitions recursively.
     *
     * Sometimes keys are all string, sometimes they are integers - we need to account for that.
     */
    private function sort(array &$data)
    {
        $keys = array_keys($data);

        if ($this->allInteger($keys)) {
            sort($data);
        } else {
            ksort($data);
        }

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $this->sort($data[$key]);
            }
        }
    }

    /**
     * @return bool
     */
    private function allInteger(array $values)
    {
        foreach ($values as $value) {
            if (!\is_int($value)) {
                return false;
            }
        }

        return true;
    }

    private function createRuleSetToTestWith(array $rules)
    {
        $ruleSet = new RuleSet();
        $reflection = new AccessibleObject($ruleSet);
        $reflection->setDefinitions = self::getRuleSetDefinitionsToTestWith();
        $reflection->set = $rules;
        $reflection->resolveSet();

        return $ruleSet;
    }

    private static function getRuleSetDefinitionsToTestWith()
    {
        static $testSet = [
            '@A' => [
                'AA' => true,
                'AB' => true,
                'AC' => 'a',
            ],
            '@B' => [
                'AA' => true,
            ],
            '@C' => [
                'AA' => false,
            ],
            '@D' => [
                'AC' => 'b',
            ],
            '@E' => [
                '@D' => true,
                'AB' => '_AB',
                'Z' => true,
            ],
            '@F' => [
                '@E' => true,
                'Z' => 'E',
            ],
            '@Foo' => ['A' => true, 'B' => true, 'C' => true, 'D' => 12],
            '@Bar' => ['A' => true, 'C' => false, 'D' => 34, 'E' => true, 'F' => false],
        ];

        return $testSet;
    }

    /**
     * @param string $ruleName
     *
     * @return string
     */
    private function getDefaultPHPUnitTargetOfRule($ruleName)
    {
        $fixer = self::getFixerByName($ruleName);

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ('target' === $option->getName()) {
                $targetVersion = $option->getDefault();

                break;
            }
        }

        if (!isset($targetVersion)) {
            static::markTestSkipped(sprintf('The fixer "%s" does not have option "target".', $fixer->getName()));
        }

        return $targetVersion;
    }

    /**
     * @param string $setName
     * @param string $ruleName
     * @param string $actualTargetVersion
     */
    private static function assertPHPUnitVersionIsLargestAllowed($setName, $ruleName, $actualTargetVersion)
    {
        $maximumVersionForRuleset = preg_replace('/^@PHPUnit(\d)(\d)Migration:risky$/', '$1.$2', $setName);

        $fixer = self::getFixerByName($ruleName);

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ('target' === $option->getName()) {
                $allowedVersionsForFixer = array_diff($option->getAllowedValues(), [PhpUnitTargetVersion::VERSION_NEWEST]);

                break;
            }
        }

        if (!isset($allowedVersionsForFixer)) {
            static::markTestSkipped(sprintf('The fixer "%s" does not have option "target".', $fixer->getName()));
        }

        $allowedVersionsForRuleset = array_filter(
            $allowedVersionsForFixer,
            function ($version) use ($maximumVersionForRuleset) {
                return strcmp($maximumVersionForRuleset, $version) >= 0;
            }
        );

        static::assertTrue(\in_array($actualTargetVersion, $allowedVersionsForRuleset, true), sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but the rule set is not allowing it (allowed are only "%s")',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            implode('", "', $allowedVersionsForRuleset)
        ));

        rsort($allowedVersionsForRuleset);
        $maximimAllowedVersionForRuleset = reset($allowedVersionsForRuleset);

        static::assertSame($maximimAllowedVersionForRuleset, $actualTargetVersion, sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but there is higher available target "%s"',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            $maximimAllowedVersionForRuleset
        ));
    }

    /**
     * @param string $name
     *
     * @return FixerInterface
     */
    private static function getFixerByName($name)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $factory->useRuleSet(new RuleSet([$name => true]));

        return current($factory->getFixers());
    }
}
