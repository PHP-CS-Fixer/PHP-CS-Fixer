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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class FixerFactoryTest extends TestCase
{
    public function testFixersPriorityEdgeFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        $this->assertSame('encoding', $fixers[0]->getName(), 'Expected "encoding" fixer to have the highest priority.');
        $this->assertSame('full_opening_tag', $fixers[1]->getName(), 'Expected "full_opening_tag" fixer has second highest priority.');
        $this->assertSame('single_blank_line_at_eof', $fixers[count($fixers) - 1]->getName(), 'Expected "single_blank_line_at_eof" to have the lowest priority.');
    }

    /**
     * @dataProvider provideFixersPriorityCases
     * @dataProvider provideFixersPrioritySpecialPhpdocCases
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority(), sprintf('"%s" should have less priority than "%s"', get_class($second), get_class($first)));
    }

    public function provideFixersPriorityCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = [];

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        return [
            [$fixers['array_syntax'], $fixers['binary_operator_spaces']],
            [$fixers['array_syntax'], $fixers['ternary_operator_spaces']],
            [$fixers['blank_line_after_opening_tag'], $fixers['no_blank_lines_before_namespace']],
            [$fixers['class_attributes_separation'], $fixers['braces']],
            [$fixers['class_attributes_separation'], $fixers['indentation_type']],
            [$fixers['class_keyword_remove'], $fixers['no_unused_imports']],
            [$fixers['combine_consecutive_issets'], $fixers['no_multiline_whitespace_before_semicolons']],
            [$fixers['combine_consecutive_issets'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['combine_consecutive_issets'], $fixers['no_trailing_whitespace']],
            [$fixers['combine_consecutive_issets'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_trailing_whitespace']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['combine_consecutive_unsets'], $fixers['space_after_semicolon']],
            [$fixers['declare_strict_types'], $fixers['blank_line_after_opening_tag']],
            [$fixers['declare_strict_types'], $fixers['declare_equal_normalize']],
            [$fixers['declare_strict_types'], $fixers['single_blank_line_before_namespace']],
            [$fixers['elseif'], $fixers['braces']],
            [$fixers['escape_implicit_backslashes'], $fixers['heredoc_to_nowdoc']],
            [$fixers['escape_implicit_backslashes'], $fixers['single_quote']],
            [$fixers['function_to_constant'], $fixers['native_function_casing']],
            [$fixers['function_to_constant'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['function_to_constant'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['function_to_constant'], $fixers['no_trailing_whitespace']],
            [$fixers['function_to_constant'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['indentation_type'], $fixers['phpdoc_indent']],
            [$fixers['is_null'], $fixers['yoda_style']],
            [$fixers['line_ending'], $fixers['single_blank_line_at_eof']],
            [$fixers['list_syntax'], $fixers['binary_operator_spaces']],
            [$fixers['list_syntax'], $fixers['ternary_operator_spaces']],
            [$fixers['method_separation'], $fixers['braces']],
            [$fixers['method_separation'], $fixers['indentation_type']],
            [$fixers['no_alias_functions'], $fixers['php_unit_dedicate_assert']],
            [$fixers['no_blank_lines_after_phpdoc'], $fixers['single_blank_line_before_namespace']],
            [$fixers['no_empty_comment'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_empty_comment'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_comment'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_phpdoc'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_empty_phpdoc'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_phpdoc'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_statement'], $fixers['braces']],
            [$fixers['no_empty_statement'], $fixers['combine_consecutive_unsets']],
            [$fixers['no_empty_statement'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_empty_statement'], $fixers['no_multiline_whitespace_before_semicolons']],
            [$fixers['no_empty_statement'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['no_empty_statement'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_statement'], $fixers['no_useless_else']],
            [$fixers['no_empty_statement'], $fixers['no_useless_return']],
            [$fixers['no_empty_statement'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_statement'], $fixers['space_after_semicolon']],
            [$fixers['no_empty_statement'], $fixers['switch_case_semicolon_to_colon']],
            [$fixers['no_leading_import_slash'], $fixers['ordered_imports']],
            [$fixers['no_multiline_whitespace_around_double_arrow'], $fixers['binary_operator_spaces']],
            [$fixers['no_multiline_whitespace_around_double_arrow'], $fixers['trailing_comma_in_multiline_array']],
            [$fixers['no_multiline_whitespace_before_semicolons'], $fixers['space_after_semicolon']],
            [$fixers['no_php4_constructor'], $fixers['ordered_class_elements']],
            [$fixers['no_short_bool_cast'], $fixers['cast_spaces']],
            [$fixers['no_short_echo_tag'], $fixers['no_mixed_echo_print']],
            [$fixers['no_spaces_after_function_name'], $fixers['function_to_constant']],
            [$fixers['no_spaces_inside_parenthesis'], $fixers['function_to_constant']],
            [$fixers['no_unneeded_control_parentheses'], $fixers['no_trailing_whitespace']],
            [$fixers['no_unneeded_curly_braces'], $fixers['no_useless_else']],
            [$fixers['no_unneeded_curly_braces'], $fixers['no_useless_return']],
            [$fixers['no_unused_imports'], $fixers['blank_line_after_namespace']],
            [$fixers['no_unused_imports'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_unused_imports'], $fixers['no_leading_import_slash']],
            [$fixers['no_useless_else'], $fixers['braces']],
            [$fixers['no_useless_else'], $fixers['combine_consecutive_unsets']],
            [$fixers['no_useless_else'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_useless_else'], $fixers['no_trailing_whitespace']],
            [$fixers['no_useless_else'], $fixers['no_useless_return']],
            [$fixers['no_useless_else'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_useless_return'], $fixers['blank_line_before_return']],
            [$fixers['no_useless_return'], $fixers['blank_line_before_statement']],
            [$fixers['no_useless_return'], $fixers['no_extra_consecutive_blank_lines']],
            [$fixers['no_useless_return'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['ordered_class_elements'], $fixers['class_attributes_separation']],
            [$fixers['ordered_class_elements'], $fixers['method_separation']],
            [$fixers['ordered_class_elements'], $fixers['no_blank_lines_after_class_opening']],
            [$fixers['ordered_class_elements'], $fixers['space_after_semicolon']],
            [$fixers['php_unit_fqcn_annotation'], $fixers['no_unused_imports']],
            [$fixers['php_unit_no_expectation_annotation'], $fixers['no_empty_phpdoc']],
            [$fixers['php_unit_no_expectation_annotation'], $fixers['php_unit_expectation']],
            [$fixers['php_unit_strict'], $fixers['php_unit_construct']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_align']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_no_access'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_access'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_no_access'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_access'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_add_missing_param_annotation']],
            [$fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_single_line_var_spacing']],
            [$fixers['phpdoc_no_empty_return'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_package'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_package'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_no_package'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_package'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_trailing_whitespace_in_comment']],
            [$fixers['phpdoc_no_useless_inheritdoc'], $fixers['phpdoc_inline_tag']],
            [$fixers['phpdoc_order'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_order'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_separation'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_summary'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_to_comment'], $fixers['no_empty_comment']],
            [$fixers['phpdoc_to_comment'], $fixers['phpdoc_no_useless_inheritdoc']],
            [$fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']],
            [$fixers['pow_to_exponentiation'], $fixers['binary_operator_spaces']],
            [$fixers['pow_to_exponentiation'], $fixers['method_argument_space']],
            [$fixers['pow_to_exponentiation'], $fixers['native_function_casing']],
            [$fixers['pow_to_exponentiation'], $fixers['no_spaces_after_function_name']],
            [$fixers['pow_to_exponentiation'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['protected_to_private'], $fixers['ordered_class_elements']],
            [$fixers['simplified_null_return'], $fixers['no_useless_return']],
            [$fixers['single_import_per_statement'], $fixers['no_leading_import_slash']],
            [$fixers['single_import_per_statement'], $fixers['no_multiline_whitespace_before_semicolons']],
            [$fixers['single_import_per_statement'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['single_import_per_statement'], $fixers['no_unused_imports']],
            [$fixers['single_import_per_statement'], $fixers['ordered_imports']],
            [$fixers['single_import_per_statement'], $fixers['space_after_semicolon']],
            [$fixers['standardize_not_equals'], $fixers['binary_operator_spaces']],
            [$fixers['strict_comparison'], $fixers['binary_operator_spaces']],
            [$fixers['unary_operator_spaces'], $fixers['not_operator_with_space']],
            [$fixers['unary_operator_spaces'], $fixers['not_operator_with_successor_space']],
            [$fixers['void_return'], $fixers['phpdoc_no_empty_return']],
            [$fixers['void_return'], $fixers['return_type_declaration']],
            [$fixers['php_unit_test_annotation'], $fixers['no_empty_phpdoc']],
            [$fixers['php_unit_test_annotation'], $fixers['phpdoc_trim']],
        ];
    }

    public function provideFixersPrioritySpecialPhpdocCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = [];

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $cases = [];

        // prepare bulk tests for phpdoc fixers to test that:
        // * `phpdoc_to_comment` is first
        // * `phpdoc_indent` is second
        // * `phpdoc_types` is third
        // * `phpdoc_scalar` is fourth
        // * `phpdoc_align` is last
        $cases[] = [$fixers['phpdoc_indent'], $fixers['phpdoc_types']];
        $cases[] = [$fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']];
        $cases[] = [$fixers['phpdoc_types'], $fixers['phpdoc_scalar']];

        $docFixerNames = array_filter(
            array_keys($fixers),
            static function ($name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!in_array($docFixerName, ['phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'], true)) {
                $cases[] = [$fixers['phpdoc_indent'], $fixers[$docFixerName]];
                $cases[] = [$fixers['phpdoc_scalar'], $fixers[$docFixerName]];
                $cases[] = [$fixers['phpdoc_to_comment'], $fixers[$docFixerName]];
                $cases[] = [$fixers['phpdoc_types'], $fixers[$docFixerName]];
            }

            if ('phpdoc_align' !== $docFixerName) {
                $cases[] = [$fixers[$docFixerName], $fixers['phpdoc_align']];
            }
        }

        return $cases;
    }

    /**
     * @dataProvider provideFixersPriorityPairsHaveIntegrationTestCases
     */
    public function testFixersPriorityPairsHaveIntegrationTest(FixerInterface $first, FixerInterface $second)
    {
        // This structure contains older cases that are not yet covered by tests.
        // It may only shrink, never add anything to it.
        $casesWithoutTests = [
            'class_attributes_separation,braces.test',
            'class_attributes_separation,indentation_type.test',
            'indentation_type,phpdoc_indent.test',
            'line_ending,single_blank_line_at_eof.test',
            'method_separation,braces.test',
            'method_separation,indentation_type.test',
            'no_empty_statement,no_multiline_whitespace_before_semicolons.test',
            'no_empty_statement,no_singleline_whitespace_before_semicolons.test',
            'php_unit_strict,php_unit_construct.test',
            'phpdoc_no_access,phpdoc_order.test',
            'phpdoc_no_access,phpdoc_separation.test',
            'phpdoc_no_empty_return,phpdoc_trim.test',
            'phpdoc_no_package,phpdoc_order.test',
            'phpdoc_no_package,phpdoc_trim.test',
            'phpdoc_order,phpdoc_separation.test',
            'phpdoc_order,phpdoc_trim.test',
            'phpdoc_separation,phpdoc_trim.test',
            'phpdoc_summary,phpdoc_trim.test',
            'phpdoc_var_without_name,phpdoc_trim.test',
            'unary_operator_spaces,not_operator_with_space.test',
            'unary_operator_spaces,not_operator_with_successor_space.test',
        ];

        $integrationTestExists = $this->doesIntegrationTestExist($first, $second);

        if (in_array($this->generateIntegrationTestName($first, $second), $casesWithoutTests, true)) {
            $this->assertFalse($integrationTestExists, sprintf('Case "%s" already has an integration test, so it should be removed from "$casesWithoutTests".', $this->generateIntegrationTestName($first, $second)));
            $this->markTestIncomplete(sprintf('Case "%s" has no integration test yet, please help and add it.', $this->generateIntegrationTestName($first, $second)));
        }

        $this->assertTrue($integrationTestExists, sprintf('There shall be an integration test "%s". How do you know that priority set up is good, if there is no integration test to check it?', $this->generateIntegrationTestName($first, $second)));
    }

    public function provideFixersPriorityPairsHaveIntegrationTestCases()
    {
        return array_filter(
            $this->provideFixersPriorityCases(),
            // ignore speed-up only priorities set up
            function (array $case) {
                return !in_array(
                    $this->generateIntegrationTestName($case[0], $case[1]),
                    [
                        'function_to_constant,native_function_casing.test',
                        'no_unused_imports,no_leading_import_slash.test',
                        'pow_to_exponentiation,method_argument_space.test',
                        'pow_to_exponentiation,native_function_casing.test',
                        'pow_to_exponentiation,no_spaces_after_function_name.test',
                        'pow_to_exponentiation,no_spaces_inside_parenthesis.test',
                    ],
                    true
                );
            }
        );
    }

    private function generateIntegrationTestName(FixerInterface $first, FixerInterface $second)
    {
        return "{$first->getName()},{$second->getName()}.test";
    }

    private function doesIntegrationTestExist(FixerInterface $first, FixerInterface $second)
    {
        return is_file(__DIR__.'/../Fixtures/Integration/priority/'.$this->generateIntegrationTestName($first, $second)) || is_file(__DIR__.'/../Fixtures/Integration/priority/'.$this->generateIntegrationTestName($second, $first));
    }
}
