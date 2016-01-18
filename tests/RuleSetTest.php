<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\FixerFactory;
use Symfony\CS\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class RuleSetTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $ruleSet = RuleSet::create();

        $this->assertInstanceOf('Symfony\CS\RuleSet', $ruleSet);
    }

    /**
     * @dataProvider provideAllRulesFromSets
     */
    public function testIfAllRulesInSetsExists($rule)
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $this->assertArrayHasKey($rule, $fixers);
    }

    public function provideAllRulesFromSets()
    {
        $cases = array();
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $cases = array_merge($cases, RuleSet::create(array($setName => true))->getRules());
        }

        return array_map(
            function ($item) {
                return array($item);
            },
            array_keys($cases)
        );
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Set "@foo" does not exist.
     */
    public function testResolveRulesWithInvalidSet()
    {
        RuleSet::create(array(
            '@foo' => true,
        ));
    }

    public function testResolveRulesWithSet()
    {
        $ruleSet = RuleSet::create(array(
            'strict' => true,
            '@PSR1' => true,
            'braces' => true,
            'encoding' => false,
            'linefeed' => true,
        ));

        $this->assertSameRules(
            array(
                'strict' => true,
                'short_tag' => true,
                'braces' => true,
                'linefeed' => true,
            ),
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithNestedSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR2' => true,
            'strict' => true,
        ));

        $this->assertSameRules(
            array(
                'encoding' => true,
                'short_tag' => true,
                'linefeed' => true,
                'indentation' => true,
                'trailing_spaces' => true,
                'php_closing_tag' => true,
                'elseif' => true,
                'visibility' => true,
                'lowercase_keywords' => true,
                'single_line_after_imports' => true,
                'switch_case_space' => true,
                'switch_case_semicolon_to_colon' => true,
                'parenthesis' => true,
                'multiple_use' => true,
                'function_call_space' => true,
                'method_argument_space' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'eof_ending' => true,
                'strict' => true,
            ),
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithDisabledSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR2' => true,
            '@PSR1' => false,
            'encoding' => true,
        ));

        $this->assertSameRules(
            array(
                'encoding' => true,
                'linefeed' => true,
                'indentation' => true,
                'trailing_spaces' => true,
                'php_closing_tag' => true,
                'elseif' => true,
                'visibility' => true,
                'lowercase_keywords' => true,
                'single_line_after_imports' => true,
                'switch_case_space' => true,
                'switch_case_semicolon_to_colon' => true,
                'parenthesis' => true,
                'multiple_use' => true,
                'function_call_space' => true,
                'method_argument_space' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'eof_ending' => true,
            ),
            $ruleSet->getRules()
        );
    }

    private function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual, $message);
    }
}
