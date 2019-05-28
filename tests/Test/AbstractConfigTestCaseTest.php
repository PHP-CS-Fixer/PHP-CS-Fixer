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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Config;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PhpCsFixer\Test\AbstractConfigTestCase;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Test\AbstractConfigTestCase
 */
final class AbstractConfigTestCaseTest extends AbstractConfigTestCase
{
    public function testAllFixersMustBeSpecified()
    {
        $config = new Config();

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('An empty config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testDisabledFixersAreOk()
    {
        $config = new Config();
        $config->setRules([
            'encoding' => false,
        ]);

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('An empty config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertNotContains('encoding', $expectationFailedException->getMessage());
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testRuleSetsAreHandled()
    {
        $config = new Config();
        $config->setRules([
            '@PSR1' => true,
        ]);

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('A partial config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertNotContains('encoding', $expectationFailedException->getMessage());
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testRuleSetsMustBeOrderedAsTheyAppearInRuleSetClass()
    {
        $config = $this->getFullConfig();
        $rules = $config->getRules();

        unset($rules['@PHPUnit35Migration:risky'], $rules['@PHPUnit30Migration:risky']);

        $rules['@PHPUnit35Migration:risky'] = true;
        $rules['@PHPUnit30Migration:risky'] = true;

        $config->setRules($rules);

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('Ruleset randomly ordered must raise an error reporting the expected order');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertNotContains('php_unit_dedicate_assert', $expectationFailedException->getMessage());
            static::assertContains(RuleSet::class, $expectationFailedException->getMessage());
        }
    }

    public function testNonExistingFixersRaiseError()
    {
        $nonExistingRule = uniqid('non_existing_rule_');

        $config = $this->getFullConfig();
        $rules = $config->getRules();
        $rules[$nonExistingRule] = true;
        $config->setRules($rules);

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('A non existing fixer must raise an error');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertContains($nonExistingRule, $expectationFailedException->getMessage());
        }
    }

    public function testFixersMustBeOrdered()
    {
        $config = $this->getFullConfig();
        $rules = $config->getRules();
        $firstRule = key($rules);
        unset($rules[$firstRule]);
        $rules[$firstRule] = true;
        $config->setRules($rules);

        try {
            $this->doTestAllDefaultRulesAreSpecified($config);
            static::fail('Fixers randomly orderer must raise an error');
        } catch (ExpectationFailedException $expectationFailedException) {
            // Can't test $firstRule because the diff isn't in the Exception
            static::assertContains('alphabetically', $expectationFailedException->getMessage());
        }
    }

    /**
     * @return Config
     */
    private function getFullConfig()
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixers = $fixerFactory->getFixers();

        $availableRules = array_filter($fixers, function (FixerInterface $fixer) {
            return !$fixer instanceof DeprecatedFixerInterface;
        });
        $availableRules = array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $availableRules);
        sort($availableRules);

        $config = new Config();
        $config->setRules(array_fill_keys($availableRules, true));

        return $config;
    }
}
