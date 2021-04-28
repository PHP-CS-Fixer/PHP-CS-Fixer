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

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\RuleSets
 */
final class RuleSetsTest extends TestCase
{
    public function testGetSetDefinitionNames()
    {
        static::assertSame(
            array_keys(RuleSets::getSetDefinitions()),
            RuleSets::getSetDefinitionNames()
        );
    }

    public function testGetSetDefinitions()
    {
        $sets = RuleSets::getSetDefinitions();

        foreach ($sets as $name => $set) {
            static::assertIsString($name);
            static::assertTrue('@' === $name[0]);
            static::assertIsArray($set->getRules());
            static::assertSame($set, RuleSets::getSetDefinition($name));
        }
    }

    public function testGetUnknownSetDefinition()
    {
        $name = 'Unknown';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(sprintf('#^Set "%s" does not exist\.$#', $name));

        RuleSets::getSetDefinition($name);
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param string $setDefinitionName
     */
    public function testHasIntegrationTest($setDefinitionName)
    {
        $setsWithoutTests = [
            '@PHP56Migration',
            '@PHP56Migration:risky',
            '@PHP70Migration',
            '@PHP70Migration:risky',
            '@PHP71Migration',
            '@PHP71Migration:risky',
            '@PHP73Migration',
            '@PHP80Migration',
            '@PhpCsFixer',
            '@PhpCsFixer:risky',
            '@PHPUnit48Migration',
            '@PHPUnit55Migration:risky',
            '@PHPUnit75Migration:risky',
            '@PHPUnit84Migration:risky',
            '@PSR1',
        ];

        if (\in_array($setDefinitionName, $setsWithoutTests, true)) {
            static::markTestIncomplete(sprintf('Set "%s" has no integration test.', $setDefinitionName));
        }

        $setDefinitionFileNamePrefix = str_replace(':', '-', $setDefinitionName);
        $dir = __DIR__.'/../../tests/Fixtures/Integration/set';
        $file = sprintf('%s/%s.test', $dir, $setDefinitionFileNamePrefix);

        static::assertFileExists($file);
        static::assertFileExists(sprintf('%s/%s.test-in.php', $dir, $setDefinitionFileNamePrefix));
        static::assertFileExists(sprintf('%s/%s.test-out.php', $dir, $setDefinitionFileNamePrefix));

        $template = '--TEST--
Integration of %s.
--RULESET--
{"%s": true}
';
        static::assertStringStartsWith(
            sprintf($template, $setDefinitionName, $setDefinitionName),
            file_get_contents($file)
        );
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param mixed $setName
     */
    public function testBuildInSetDefinitionNames($setName)
    {
        static::assertIsString($setName);
        static::assertSame('@', substr($setName, 0, 1));
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     *
     * @param string $setDefinitionName
     */
    public function testSetDefinitionsAreSorted($setDefinitionName)
    {
        $setDefinition = RuleSets::getSetDefinitions()[$setDefinitionName]->getRules();
        $sortedSetDefinition = $setDefinition;
        $this->sort($sortedSetDefinition);

        static::assertSame($sortedSetDefinition, $setDefinition, sprintf(
            'Failed to assert that the set definition for "%s" is sorted by key.',
            $setDefinitionName
        ));
    }

    public function testSetDefinitionsItselfIsSorted()
    {
        $setDefinition = array_keys(RuleSets::getSetDefinitions());
        $sortedSetDefinition = $setDefinition;
        $this->sort($sortedSetDefinition);

        static::assertSame($sortedSetDefinition, $setDefinition);
    }

    /**
     * @return array
     */
    public function provideSetDefinitionNameCases()
    {
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        return array_map(static function ($setDefinitionName) {
            return [$setDefinitionName];
        }, $setDefinitionNames);
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
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        $setDefinitionPHPUnitMigrationNames = array_filter($setDefinitionNames, static function ($setDefinitionName) {
            return 1 === preg_match('/^@PHPUnit\d{2}Migration:risky$/', $setDefinitionName);
        });

        return array_map(static function ($setDefinitionName) {
            return [$setDefinitionName];
        }, $setDefinitionPHPUnitMigrationNames);
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
            throw new \Exception(sprintf('The fixer "%s" does not have option "target".', $fixer->getName()));
        }

        $allowedVersionsForRuleset = array_filter(
            $allowedVersionsForFixer,
            static function ($version) use ($maximumVersionForRuleset) {
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
        $maximumAllowedVersionForRuleset = reset($allowedVersionsForRuleset);

        static::assertSame($maximumAllowedVersionForRuleset, $actualTargetVersion, sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but there is higher available target "%s"',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            $maximumAllowedVersionForRuleset
        ));
    }

    /**
     * Sorts an array of rule set definitions recursively.
     *
     * Sometimes keys are all string, sometimes they are integers - we need to account for that.
     */
    private function sort(array &$data)
    {
        $this->doSort($data, '');
    }

    /**
     * @param string $path
     */
    private function doSort(array &$data, $path)
    {
        if ('ordered_imports.imports_order' === $path) { // order matters
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

    /**
     * @param string $ruleName
     *
     * @return string
     */
    private function getDefaultPHPUnitTargetOfRule($ruleName)
    {
        $targetVersion = null;
        $fixer = self::getFixerByName($ruleName);

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ('target' === $option->getName()) {
                $targetVersion = $option->getDefault();

                break;
            }
        }

        if (null === $targetVersion) {
            throw new \Exception(sprintf('The fixer "%s" does not have option "target".', $fixer->getName()));
        }

        return $targetVersion;
    }

    /**
     * @param string $name
     *
     * @return AbstractFixer
     */
    private static function getFixerByName($name)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $factory->useRuleSet(new RuleSet([$name => true]));

        $fixers = $factory->getFixers();

        if (empty($fixers)) {
            throw new \RuntimeException('FixerFactory unexpectedly returned empty array.');
        }

        $fixer = current($fixers);

        if (!$fixer instanceof AbstractFixer) {
            throw new \RuntimeException(sprintf('Fixer class for "%s" rule does not extend "%s".', $name, AbstractFixer::class));
        }

        return $fixer;
    }
}
