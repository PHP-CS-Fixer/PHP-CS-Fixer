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

use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

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

        $this->assertInstanceOf('PhpCsFixer\RuleSet', $ruleSet);
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
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Set "@foo" does not exist.
     */
    public function testResolveRulesWithInvalidSet()
    {
        RuleSet::create(array(
            '@foo' => true,
        ));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Missing value for "braces" rule/set.
     */
    public function testResolveRulesWithMissingRuleValue()
    {
        RuleSet::create(array(
            'braces',
        ));
    }

    public function testResolveRulesWithSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR1' => true,
            'braces' => true,
            'encoding' => false,
            'strict_comparison' => true,
            'line_ending' => true,
        ));

        $this->assertSameRules(
            array(
                'braces' => true,
                'full_opening_tag' => true,
                'strict_comparison' => true,
                'line_ending' => true,
            ),
            $ruleSet->getRules()
        );
    }

    public function testResolveRulesWithNestedSet()
    {
        $ruleSet = RuleSet::create(array(
            '@PSR2' => true,
            'strict_comparison' => true,
        ));

        $this->assertSameRules(
            array(
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'elseif' => true,
                'encoding' => true,
                'full_opening_tag' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'lowercase_keywords' => true,
                'method_argument_space' => true,
                'no_closing_tag' => true,
                'no_spaces_after_function_name' => true,
                'no_spaces_inside_parenthesis' => true,
                'indentation_type' => true,
                'no_trailing_whitespace' => true,
                'no_trailing_whitespace_in_comment' => true,
                'single_blank_line_at_eof' => true,
                'single_import_per_statement' => true,
                'single_line_after_imports' => true,
                'single_class_element_per_statement' => array('property'),
                'strict_comparison' => true,
                'switch_case_semicolon_to_colon' => true,
                'switch_case_space' => true,
                'line_ending' => true,
                'visibility_required' => true,
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
                'blank_line_after_namespace' => true,
                'braces' => true,
                'class_definition' => true,
                'elseif' => true,
                'encoding' => true,
                'function_declaration' => true,
                'lowercase_constants' => true,
                'lowercase_keywords' => true,
                'method_argument_space' => true,
                'no_closing_tag' => true,
                'no_spaces_after_function_name' => true,
                'no_spaces_inside_parenthesis' => true,
                'indentation_type' => true,
                'no_trailing_whitespace' => true,
                'no_trailing_whitespace_in_comment' => true,
                'single_blank_line_at_eof' => true,
                'single_import_per_statement' => true,
                'single_line_after_imports' => true,
                'single_class_element_per_statement' => array('property'),
                'switch_case_semicolon_to_colon' => true,
                'switch_case_space' => true,
                'line_ending' => true,
                'visibility_required' => true,
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
