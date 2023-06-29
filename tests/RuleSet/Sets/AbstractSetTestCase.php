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

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 */
abstract class AbstractSetTestCase extends TestCase
{
    public function testSet(): void
    {
        $set = self::getSet();

        $setName = $set->getName();
        $setDescription = $set->getDescription();
        $isRiskySet = $set->isRisky();
        $setRules = $set->getRules();

        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        self::assertSanityString($setName);
        self::assertSanityString($setDescription);
        self::assertStringEndsWith('.', $setDescription, sprintf('Ruleset description of "%s" must end with ".", got "%s".', $setName, $setDescription));
        self::assertRules($setRules, $factory, $setName);

        if (1 === preg_match('/(\d+)(\d)Migration/', \get_class($set), $matches)) {
            self::assertStringEndsWith(
                sprintf(' %d.%d compatibility.', $matches[1], $matches[2]),
                $setDescription,
                sprintf('Set %s has incorrect description: "%s".', $setName, $setDescription)
            );
        }

        try {
            $factory->useRuleSet(new RuleSet($set->getRules()));
        } catch (InvalidForEnvFixerConfigurationException $e) {
            self::markTestSkipped(sprintf('Cannot test set "%s" on this environment. %s', $setName, $e->getMessage()));
        }

        foreach ($factory->getFixers() as $fixer) {
            $fixerName = $fixer->getName();
            self::assertSame($isRiskySet, $fixer->isRisky(), sprintf('Is risky mismatch between set "%s" and rule "%s".', $setName, $fixerName));

            if (isset($setRules[$fixerName])) {
                self::assertTrue(\is_bool($setRules[$fixerName]) || \is_array($setRules[$fixerName]));
            }
        }
    }

    protected static function assertSanityString(string $string): void
    {
        self::assertSame(trim($string), $string);
        self::assertNotSame('', $string);
    }

    protected static function getSet(): RuleSetDescriptionInterface
    {
        $setClassName = preg_replace('/^(PhpCsFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $setClassName();
    }

    /**
     * @param array<string, array<string, mixed>|bool> $setRules
     */
    protected static function assertRules(array $setRules, FixerFactory $factory, string $setName): void
    {
        $sawRule = false;

        foreach ($setRules as $rule => $config) {
            self::assertIsString($rule, $setName);

            if (str_starts_with($rule, '@')) {
                self::assertFalse($sawRule, sprintf('Ruleset "%s" should define all sets it extends first and than list by rule configuration overrides.', $setName));
                RuleSets::getSetDefinition($setName);
            } else {
                $sawRule = true;
                self::assertTrue($factory->hasRule($rule), $rule);
            }
        }

        $setRulesSorted = $setRules;
        ksort($setRulesSorted);

        self::assertSame($setRulesSorted, $setRules);
    }
}
