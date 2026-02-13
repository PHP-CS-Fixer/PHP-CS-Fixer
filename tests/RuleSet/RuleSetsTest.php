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

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\Fixtures\ExternalRuleSet\ExampleRuleSet;
use PhpCsFixer\Tests\Test\CiReader;
use PhpCsFixer\Tests\Test\TestCaseUtils;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\RuleSets
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RuleSetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Since we register custom rule sets statically, we need to clear custom rule sets between runs.
        // We don't need to clear the built-in rule sets, because they don't change between runs.
        \Closure::bind(
            static function (): void { RuleSets::$customRuleSetDefinitions = []; },
            null,
            RuleSets::class,
        )();
    }

    public function testGetSetDefinitionNames(): void
    {
        self::assertSame(
            array_keys(RuleSets::getSetDefinitions()),
            RuleSets::getSetDefinitionNames(),
        );
    }

    public function testGetSetDefinitions(): void
    {
        $sets = RuleSets::getBuiltInSetDefinitions();

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
        $this->expectExceptionMessageMatches(\sprintf('#^Set "%s" does not exist\.$#', $name));

        RuleSets::getSetDefinition($name);
    }

    public function testThatPhpMigrationSetsAreDefinedForEachSupportedPhpVersion(): void
    {
        $supportedPhpVersions = CiReader::getAllPhpVersionsUsedByCiForTests();

        $sets = RuleSets::getSetDefinitions();
        self::assertNotEmpty($supportedPhpVersions);
        foreach ($supportedPhpVersions as $version) {
            foreach (['', ':risky'] as $suffix) {
                $setName = \sprintf('@PHP%sMigration%s', str_replace('.', 'x', $version), $suffix);
                // var_dump($setName);
                self::assertArrayHasKey($setName, $sets, \sprintf('Set "%s" is not defined.', $setName));
            }
        }
    }

    /**
     * @dataProvider provideSetDefinitionNameCases
     */
    public function testHasIntegrationTest(string $setDefinitionName): void
    {
        /** @TODO v4 remove deprecated sets */
        $setsWithoutTests = [
            '@PER-CS:risky',
            '@PER-CS',
            '@PER-CS1.0:risky',
            '@PER-CS1.0',
            '@PER-CS2.0:risky',
            '@PER-CS2.0',
            '@PER-CS3.0:risky',
            '@PER-CS3.0',
            '@PER:risky',
            '@PER',
            '@PHP5x6Migration:risky',
            '@PHP5x6Migration',
            '@PHP7x0Migration:risky',
            '@PHP7x0Migration',
            '@PHP7x1Migration:risky',
            '@PHP7x1Migration',
            '@PHP7x3Migration',
            '@PHP8x0Migration',
            '@PhpCsFixer:risky',
            '@PhpCsFixer',
            '@PHPUnit10x0Migration:risky',
            '@PHPUnit11x0Migration:risky',
            '@PHPUnit4x8Migration',
            '@PHPUnit5x5Migration:risky',
            '@PHPUnit7x5Migration:risky',
            '@PHPUnit8x4Migration:risky',
            '@PHPUnit9x1Migration:risky',
            '@PSR1',
        ];

        if (\in_array($setDefinitionName, $setsWithoutTests, true)) {
            self::markTestIncomplete(\sprintf('Set "%s" has no integration test.', $setDefinitionName));
        }

        // @TODO v4: remove me @MARKER_deprecated_migration_ruleset
        if (Preg::match('/^@PHP(Unit)?\d+Migration(:risky)?$/', $setDefinitionName)) {
            self::markTestSkipped(\sprintf('Set "%s" is deprecated and will be removed in next MAJOR.', $setDefinitionName));
        }

        if (str_starts_with($setDefinitionName, '@auto')) {
            self::markTestSkipped(\sprintf('Set "%s" is automatic and it\'s definition depends on individual project.', $setDefinitionName));
        }

        \assert(\array_key_exists($setDefinitionName, RuleSets::getSetDefinitions()));
        $setDefinition = RuleSets::getSetDefinitions()[$setDefinitionName]->getRules();

        if (1 === \count($setDefinition)
            && str_starts_with($setDefinitionName, '@PHP')
            && str_starts_with(array_key_first($setDefinition), '@PHP')
        ) {
            self::markTestSkipped(\sprintf('Set "%s" only includes previous, no own rules to test.', $setDefinitionName));
        }

        $setDefinitionFileNamePrefix = str_replace(':', '-', $setDefinitionName);
        $dir = __DIR__.'/../../tests/Fixtures/Integration/set';
        $file = \sprintf('%s/%s.test', $dir, $setDefinitionFileNamePrefix);

        self::assertFileExists($file);
        self::assertFileExists(\sprintf('%s/%s.test-in.php', $dir, $setDefinitionFileNamePrefix));
        self::assertFileExists(\sprintf('%s/%s.test-out.php', $dir, $setDefinitionFileNamePrefix));

        $template = '--TEST--
Integration of %s.
--RULESET--
{"%s": true}
';
        self::assertStringStartsWith(
            \sprintf($template, $setDefinitionName, $setDefinitionName),
            (string) file_get_contents($file),
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
        \assert(\array_key_exists($setDefinitionName, RuleSets::getSetDefinitions()));
        $setDefinition = RuleSets::getSetDefinitions()[$setDefinitionName]->getRules();
        $sortedSetDefinition = $setDefinition;
        $this->sort($sortedSetDefinition);

        self::assertSame($sortedSetDefinition, $setDefinition, \sprintf(
            'Failed to assert that the set definition for "%s" is sorted by key.',
            $setDefinitionName,
        ));
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideSetDefinitionNameCases(): iterable
    {
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        return array_map(static fn (string $setDefinitionName): array => [$setDefinitionName], $setDefinitionNames);
    }

    public function testSetDefinitionsItselfIsSorted(): void
    {
        $setDefinition = array_keys(RuleSets::getSetDefinitions());
        $sortedSetDefinition = $setDefinition;
        natcasesort($sortedSetDefinition);

        self::assertSame($sortedSetDefinition, $setDefinition);
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
     * @return iterable<int, array{string}>
     */
    public static function providePHPUnitMigrationTargetVersionsCases(): iterable
    {
        $setDefinitionNames = RuleSets::getSetDefinitionNames();

        $setDefinitionPHPUnitMigrationNames = array_filter($setDefinitionNames, static fn (string $setDefinitionName): bool => Preg::match('/^@PHPUnit\d+Migration:risky$/', $setDefinitionName));

        return array_map(static fn (string $setDefinitionName): array => [$setDefinitionName], $setDefinitionPHPUnitMigrationNames);
    }

    public function testRegisteringRulesetMultipleTimesCausesAnException(): void
    {
        RuleSets::registerCustomRuleSet(new ExampleRuleSet());
        self::expectException(\InvalidArgumentException::class);
        RuleSets::registerCustomRuleSet(new ExampleRuleSet());
    }

    public function testCanReadCustomRegisteredRuleSet(): void
    {
        RuleSets::registerCustomRuleSet(new ExampleRuleSet());
        $set = RuleSets::getSetDefinition('@Vendor/RuleSet');
        self::assertSame('@Vendor/RuleSet', $set->getName());
    }

    private static function assertPHPUnitVersionIsLargestAllowed(string $setName, string $ruleName, string $actualTargetVersion): void
    {
        $maximumVersionForRuleset = Preg::replace('/^@PHPUnit(\d+)(\d)Migration:risky$/', '$1.$2', $setName);

        $fixer = TestCaseUtils::getFixerByName($ruleName);

        self::assertInstanceOf(ConfigurableFixerInterface::class, $fixer, \sprintf('The fixer "%s" shall be configurable.', $fixer->getName()));

        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ('target' === $option->getName()) {
                /** @var non-empty-list<PhpUnitTargetVersion::VERSION_*> */
                $allowedValues = $option->getAllowedValues();

                $allowedVersionsForFixer = array_diff(
                    $allowedValues,
                    [PhpUnitTargetVersion::VERSION_NEWEST],
                );

                break;
            }
        }

        if (!isset($allowedVersionsForFixer)) {
            throw new \Exception(\sprintf('The fixer "%s" does not have option "target".', $fixer->getName()));
        }

        /** @var list<PhpUnitTargetVersion::VERSION_*> */
        $allowedVersionsForRuleset = array_filter(
            $allowedVersionsForFixer,
            static fn (string $version): bool => version_compare($maximumVersionForRuleset, $version) >= 0,
        );

        self::assertTrue(\in_array($actualTargetVersion, $allowedVersionsForRuleset, true), \sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but the rule set is not allowing it (allowed are only "%s")',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            implode('", "', $allowedVersionsForRuleset),
        ));

        rsort($allowedVersionsForRuleset);
        $maximumAllowedVersionForRuleset = reset($allowedVersionsForRuleset);

        self::assertSame($maximumAllowedVersionForRuleset, $actualTargetVersion, \sprintf(
            'Rule "%s" (in rule set "%s") has target "%s", but there is higher available target "%s"',
            $fixer->getName(),
            $setName,
            $actualTargetVersion,
            $maximumAllowedVersionForRuleset,
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
        if (\in_array($path, ['ordered_imports.imports_order', 'phpdoc_order.order'], true)) { // order matters
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
                    $path.('' !== $path ? '.' : '').$key,
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
        $fixer = TestCaseUtils::getFixerByName($ruleName);

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
}
