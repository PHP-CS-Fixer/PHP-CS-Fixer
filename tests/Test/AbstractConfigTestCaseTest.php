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
    public function testAllFixersMustBeConfigured()
    {
        // Good run
        $this->doTestAllBuiltInRulesAreConfigured($this->getFullConfig());

        // Bad run
        $config = new Config();

        try {
            $this->doTestAllBuiltInRulesAreConfigured($config);
            static::fail('An empty config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testDisabledFixersAreOk()
    {
        // Good run
        $fullConfig = $this->getFullConfig();
        $fullConfigRules = $fullConfig->getRules();
        $fullConfigRules['encoding'] = false;
        $fullConfig->setRules($fullConfigRules);
        $this->doTestAllBuiltInRulesAreConfigured($fullConfig);

        // Bad run
        $config = new Config();
        $config->setRules([
            'encoding' => false,
        ]);

        try {
            $this->doTestAllBuiltInRulesAreConfigured($config);
            static::fail('An empty config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertNotContains('encoding', $expectationFailedException->getMessage(), 'Disabled fixers should not appear in the error message');
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testSetDefinitionsAreHandled()
    {
        $config = new Config();
        $config->setRules([
            '@PSR1' => true,
        ]);

        try {
            $this->doTestAllBuiltInRulesAreConfigured($config);
            static::fail('A partial config must raise an error reporting the missing fixers');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertNotContains('encoding', $expectationFailedException->getMessage(), 'Fixers inside set definitions should not appear in the error message');
            static::assertContains('array_syntax', $expectationFailedException->getMessage());
        }
    }

    public function testSetDefinitionsMustAppearBeforeRules()
    {
        // Good run
        $fullConfig = $this->getFullConfig();
        $rules = [
            '@PSR1' => true,
        ];
        $fullConfig->setRules(array_merge($rules, $fullConfig->getRules()));
        $this->doTestAllBuiltInRulesAreConfigured($fullConfig);

        // Bad run
        $fullConfig = $this->getFullConfig();
        $fullConfigRules = $fullConfig->getRules();
        $fullConfigRules['@PSR1'] = true;
        $fullConfig->setRules($fullConfigRules);

        try {
            $this->doTestAllBuiltInRulesAreConfigured($fullConfig);
            static::fail('Set definitions not on the top of the rule list');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertContains('@PSR1', $expectationFailedException->getMessage());
            static::assertContains('overwrite', $expectationFailedException->getMessage());
        }
    }

    public function testSetDefinitionsMustBeOrderedAsTheyAppearInRuleSetClass()
    {
        // Good run
        $fullConfig = $this->getFullConfig();
        $rules = [
            '@PHPUnit30Migration:risky' => true,
            '@PHPUnit35Migration:risky' => true,
        ];
        $fullConfig->setRules(array_merge($rules, $fullConfig->getRules()));
        $this->doTestAllBuiltInRulesAreConfigured($fullConfig);

        // Bad run
        $fullConfig = $this->getFullConfig();
        $rules = [
            '@PHPUnit35Migration:risky' => true,
            '@PHPUnit30Migration:risky' => true,
        ];
        $fullConfig->setRules(array_merge($rules, $fullConfig->getRules()));

        try {
            $this->doTestAllBuiltInRulesAreConfigured($fullConfig);
            static::fail('Set definitions randomly ordered must raise an error reporting the expected order');
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
            $this->doTestAllBuiltInRulesAreConfigured($config);
            static::fail('A non existing fixer must raise an error');
        } catch (ExpectationFailedException $expectationFailedException) {
            static::assertContains($nonExistingRule, $expectationFailedException->getMessage());
        }
    }

    public function testConfiguredRulesMustBeInAlphabeticalOrdered()
    {
        $config = $this->getFullConfig();
        $rules = $config->getRules();
        $firstRule = key($rules);
        unset($rules[$firstRule]);
        $rules[$firstRule] = true;
        $config->setRules($rules);

        try {
            $this->doTestAllBuiltInRulesAreConfigured($config);
            static::fail('Fixers randomly orderer must raise an error');
        } catch (ExpectationFailedException $expectationFailedException) {
            // Can't test $firstRule because the diff isn't in the Exception
            static::assertContains('alphabetical order', $expectationFailedException->getMessage());
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
