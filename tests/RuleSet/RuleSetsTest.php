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

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
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
    public function testGetSetDefinitionNames(): void
    {
        self::assertSame(
            array_keys(RuleSets::getSetDefinitions()),
            RuleSets::getSetDefinitionNames()
        );
    }

    public function testGetSetDefinitions(): void
    {
        $sets = RuleSets::getSetDefinitions();

        foreach ($sets as $name => $set) {
            self::assertIsString($name);
            self::assertStringStartsWith('@', $name);
            self::assertIsArray($set->getRules());
            self::assertSame($set, RuleSets::getSetDefinition($name));
        }
    }

    public function testGetUnknownSetDefinition(): void
    {
        $name = 'Unknown';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(sprintf('#^Set "%s" does not exist\.$#', $name));

        RuleSets::getSetDefinition($name);
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     */
    public function testHasIntegrationTest(string $setDefinitionName): void
    {
        $setsWithoutTests = [
            '@PER',
            '@PER:risky',
            '@PER-CS',
            '@PER-CS:risky',
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
            '@PHPUnit100Migration:risky',
            '@PSR1',
        ];

        if (\in_array($setDefinitionName, $setsWithoutTests, true)) {
            self::markTestIncomplete(sprintf('Set "%s" has no integration test.', $setDefinitionName));
        }

        $setDefinitionFileNamePrefix = str_replace(':', '-', $setDefinitionName);
        $dir = __DIR__.'/../../tests/Fixtures/Integration/set';
        $file = sprintf('%s/%s.test', $dir, $setDefinitionFileNamePrefix);

        self::assertFileExists($file);
        self::assertFileExists(sprintf('%s/%s.test-in.php', $dir, $setDefinitionFileNamePrefix));
        self::assertFileExists(sprintf('%s/%s.test-out.php', $dir, $setDefinitionFileNamePrefix));

        $template = '--TEST--
Integration of %s.
--RULESET--
{"%s": true}
';
        self::assertStringStartsWith(
            sprintf($template, $setDefinitionName, $setDefinitionName),
            file_get_contents($file)
        );
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     */
    public function testBuildInSetDefinitionNames(string $setName): void
    {
        self::assertStringStartsWith('@', $setName);
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     */
    public function testSetDefinitionsAreSorted(string $setDefinitionName): void
    {
        $setDefinition = RuleSets::getSetDefinitions()[$setDefinitionName]->getRules();
        $sortedSetDefinition = $setDefinition;
        $this->sort($sortedSetDefinition);

        self::assertSame($sortedSetDefinition, $setDefinition, sprintf(
            'Failed to assert that the set definition for "%s" is sorted by key.',
            $setDefinitionName
        ));
    }

    public function testSetDefinitionsItselfIsSorted(): void
    {
        $setDefinition = array_keys(RuleSets::getSetDefinitions());
        $sortedSetDefinition = $setDefinition;
        natsort($sortedSetDefinition);

        self::assertSame($sortedSetDefinition, $setDefinition);
    }

    public static function provideSetDefinitionNameCases(): iterable
    {
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        return array_map(static fn (string $setDefinitionName): array => [$setDefinitionName], $setDefinitionNames);
    }

    /**
     * @dataProvider providePHPUnitMigrationTargetVersionsCases
     */
    public function testPHPUnitMigrationTargetVersions(string $setName): void
    {
        $ruleSet = new RuleSet([$setName => true]);

        foreach ($ruleSet->getRules() as $ruleName => $ruleConfig) {
            $targetVersion = $ruleConfig['target'] ?? $this->getDefaultPHPUnitTargetOfRule($ruleName);

            if (null === $targetVersion) {
                // fixer does not have "target" option
                $this->addToAssertionCount(1);

                continue;
            }

            self::assertPHPUnitVersionIsLargestAllowed($setName, $ruleName, $targetVersion);
        }
    }

    /**
     * @return iterable<array{string}>
     */
    public static function providePHPUnitMigrationTargetVersionsCases(): iterable
    {
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        $setDefinitionPHPUnitMigrationNames = array_filter($setDefinitionNames, static fn (string $setDefinitionName): bool => 1 === preg_match('/^@PHPUnit\d+Migration:risky$/', $setDefinitionName));

        return array_map(static fn (string $setDefinitionName): array => [$setDefinitionName], $setDefinitionPHPUnitMigrationNames);
    }

    private static function assertPHPUnitVersionIsLargestAllowed(string $setName, string $ruleName, string $actualTargetVersion): void
    {
        $maximumVersionForRuleset = preg_replace('/^@PHPUnit(\d+)(\d)Migration:risky$/', '$1.$2', $setName);

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

        /** @var string[] $allowedVersionsForRuleset */
        $allowedVersionsForRuleset = array_filter(
            $allowedVersionsForFixer,
            static fn (string $version): bool => version_compare($maximumVersionForRuleset, $version) >= 0
        );

        self::assertTrue(\in_array($actualTargetVersion, $allowedVersionsForRuleset, true), sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but the rule set is not allowing it (allowed are only "%s")',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            implode('", "', $allowedVersionsForRuleset)
        ));

        rsort($allowedVersionsForRuleset);
        $maximumAllowedVersionForRuleset = reset($allowedVersionsForRuleset);

        self::assertSame($maximumAllowedVersionForRuleset, $actualTargetVersion, sprintf(
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
     *
     * @param array<array-key, mixed> $data
     */
    private function sort(array &$data): void
    {
        $this->doSort($data, '');
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function doSort(array &$data, string $path): void
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
     * @param array<array-key, mixed> $values
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

    private function getDefaultPHPUnitTargetOfRule(string $ruleName): ?string
    {
        $targetVersion = null;
        $fixer = self::getFixerByName($ruleName);

        if ($fixer instanceof ConfigurableFixerInterface) {
            foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
                if ('target' === $option->getName()) {
                    $targetVersion = $option->getDefault();

                    break;
                }
            }
        }

        return $targetVersion;
    }

    private static function getFixerByName(string $name): AbstractFixer
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $factory->useRuleSet(new RuleSet([$name => true]));

        $fixers = $factory->getFixers();

        if (0 === \count($fixers)) {
            throw new \RuntimeException('FixerFactory unexpectedly returned empty array.');
        }

        $fixer = current($fixers);

        if (!$fixer instanceof AbstractFixer) {
            throw new \RuntimeException(sprintf('Fixer class for "%s" rule does not extend "%s".', $name, AbstractFixer::class));
        }

        return $fixer;
    }
}
