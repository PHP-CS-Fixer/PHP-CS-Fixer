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

namespace PhpCsFixer\Test;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PHPUnit\Framework\TestCase;

abstract class AbstractConfigTestCase extends TestCase
{
    final protected function doTestAllDefaultRulesAreSpecified(ConfigInterface $config)
    {
        $configRules = $config->getRules();
        $ruleSet = new RuleSet($configRules);
        $rules = $ruleSet->getRules();

        // RuleSet strips all disabled rules
        foreach ($configRules as $name => $value) {
            if ('@' === $name[0]) {
                continue;
            }
            $rules[$name] = $value;
        }

        $currentRules = array_keys($rules);
        sort($currentRules);

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixerFactory->registerCustomFixers($config->getCustomFixers());
        $fixers = $fixerFactory->getFixers();

        $availableRules = array_filter($fixers, function (FixerInterface $fixer) {
            return !$fixer instanceof DeprecatedFixerInterface;
        });
        $availableRules = array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $availableRules);
        sort($availableRules);

        $diff = array_diff($availableRules, $currentRules);
        static::assertEmpty($diff, sprintf("The following fixers are missing:\n- %s", implode(\PHP_EOL.'- ', $diff)));

        $diff = array_diff($currentRules, $availableRules);
        static::assertEmpty($diff, sprintf("The following fixers are specified but non existing or deprecated:\n- %s", implode(\PHP_EOL.'- ', $diff)));

        $currentRules = array_keys($configRules);
        $orderedCurrentRules = $currentRules;
        sort($orderedCurrentRules);
        static::assertSame($orderedCurrentRules, $currentRules, 'Fixers must be alphabetically ordered');
    }
}
