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

namespace PhpCsFixer\Tests\RuleSet\Sets;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet\AutomaticRuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;
use PhpCsFixer\Tests\Test\TestCaseUtils;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\Sets\AutoPHPUnitMigrationRiskySet
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AutoPHPUnitMigrationRiskySetTest extends AbstractSetTestCase
{
    /**
     * @covers \PhpCsFixer\RuleSet\AutomaticMigrationSetTrait
     */
    public function testCorrectResolutionTowardsOurOwnRepoConfig(): void
    {
        $set = self::getSet();
        $rules = $set->getRules();

        self::assertSame(
            ['@PHPUnit9x1Migration:risky' => true], // bump me when updating minimum supported version in composer.json
            $rules,
        );
    }

    /**
     * @dataProvider provideThatSetDoNotUseNewestTargetCases
     */
    public function testThatSetDoNotUseNewestTarget(string $setName): void
    {
        $versionInRuleName = Preg::replace('/^@PHPUnit(\d+)x(\d+)Migration:risky$/', '$1.$2', $setName);

        $setClassName = 'PhpCsFixer\RuleSet\Sets\\'.str_replace(['@', ':risky'], ['', 'Risky'], $setName).'Set';
        $set = new $setClassName();
        \assert($set instanceof RuleSetDefinitionInterface);

        $rulesWithTarget = array_filter(
            $set->getRules(),
            static fn ($configuration) => \is_array($configuration) && isset($configuration['target']),
        );

        if ([] === $rulesWithTarget) {
            $this->addToAssertionCount(1); // no rule that is configured with 'target'

            return;
        }

        foreach ($rulesWithTarget as $rule => $configuration) {
            if (!\is_array($configuration) || !isset($configuration['target'])) {
                continue;
            }

            self::assertNotSame(PhpUnitTargetVersion::VERSION_NEWEST, $configuration['target']);
            self::assertSame($versionInRuleName, $configuration['target']);
        }
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideThatSetDoNotUseNewestTargetCases(): iterable
    {
        $setDefinition = self::getSet();
        \assert($setDefinition instanceof AutomaticRuleSetDefinitionInterface);

        $sets = array_keys($setDefinition->getRulesCandidates());

        foreach ($sets as $set) {
            yield $set => [$set];
        }
    }

    public function testThatHighestSetUsesHighestTargets(): void
    {
        $setDefinition = self::getSet();
        \assert($setDefinition instanceof AutomaticRuleSetDefinitionInterface);

        $highestSet = array_key_last($setDefinition->getRulesCandidates());
        $ruleSet = new RuleSet([$highestSet => true]);

        foreach ($ruleSet->getRules() as $rule => $configuration) {
            self::assertNotFalse($configuration);
            self::assertSame(
                self::getHighestConfigurationForPHPUnitFixer($rule),
                $configuration['target'] ?? null,
                \sprintf(
                    'Rule "%s" configuration is not the highest one.',
                    $rule,
                ),
            );
        }
    }

    private static function getHighestConfigurationForPHPUnitFixer(string $rule): ?string
    {
        $fixer = TestCaseUtils::getFixerByName($rule);

        if (!$fixer instanceof ConfigurableFixerInterface) {
            return null;
        }

        $options = $fixer->getConfigurationDefinition()->getOptions();
        $targetOption = array_find(
            $options,
            static fn ($option) => 'target' === $option->getName(),
        );

        if (null === $targetOption) {
            return null;
        }

        $allowedVersionValues = array_filter(
            $targetOption->getAllowedValues(),
            static fn ($value) => PhpUnitTargetVersion::VERSION_NEWEST !== $value,
        );
        natsort($allowedVersionValues);

        return array_last($allowedVersionValues);
    }
}
