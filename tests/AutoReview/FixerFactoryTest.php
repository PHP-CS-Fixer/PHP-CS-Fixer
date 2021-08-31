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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\Test\IntegrationCaseFactory;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class FixerFactoryTest extends TestCase
{
    public function testFixersPriorityEdgeFixers(): void
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        static::assertSame('encoding', $fixers[0]->getName(), 'Expected "encoding" fixer to have the highest priority.');
        static::assertSame('full_opening_tag', $fixers[1]->getName(), 'Expected "full_opening_tag" fixer has second highest priority.');
        static::assertSame('single_blank_line_at_eof', $fixers[\count($fixers) - 1]->getName(), 'Expected "single_blank_line_at_eof" to have the lowest priority.');
    }

    /**
     * @dataProvider provideFixersPriorityCases
     * @dataProvider provideFixersPrioritySpecialPhpdocCases
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second): void
    {
        static::assertLessThan($first->getPriority(), $second->getPriority(), sprintf('"%s" should have less priority than "%s"', \get_class($second), \get_class($first)));
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
            [$fixers['align_multiline_comment'], $fixers['phpdoc_trim_consecutive_blank_line_separation']],
            [$fixers['array_indentation'], $fixers['align_multiline_comment']],
            [$fixers['array_indentation'], $fixers['binary_operator_spaces']],
            [$fixers['array_syntax'], $fixers['binary_operator_spaces']],
            [$fixers['array_syntax'], $fixers['ternary_operator_spaces']],
            [$fixers['backtick_to_shell_exec'], $fixers['escape_implicit_backslashes']],
            [$fixers['backtick_to_shell_exec'], $fixers['explicit_string_variable']],
            [$fixers['backtick_to_shell_exec'], $fixers['native_function_invocation']],
            [$fixers['backtick_to_shell_exec'], $fixers['single_quote']],
            [$fixers['blank_line_after_opening_tag'], $fixers['no_blank_lines_before_namespace']],
            [$fixers['braces'], $fixers['array_indentation']],
            [$fixers['braces'], $fixers['method_argument_space']],
            [$fixers['braces'], $fixers['method_chaining_indentation']],
            [$fixers['class_attributes_separation'], $fixers['braces']],
            [$fixers['class_attributes_separation'], $fixers['indentation_type']],
            [$fixers['class_attributes_separation'], $fixers['no_extra_blank_lines']],
            [$fixers['class_definition'], $fixers['braces']],
            [$fixers['class_keyword_remove'], $fixers['no_unused_imports']],
            [$fixers['combine_consecutive_issets'], $fixers['multiline_whitespace_before_semicolons']],
            [$fixers['combine_consecutive_issets'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['combine_consecutive_issets'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['combine_consecutive_issets'], $fixers['no_trailing_whitespace']],
            [$fixers['combine_consecutive_issets'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_extra_blank_lines']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_trailing_whitespace']],
            [$fixers['combine_consecutive_unsets'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['combine_consecutive_unsets'], $fixers['space_after_semicolon']],
            [$fixers['combine_nested_dirname'], $fixers['method_argument_space']],
            [$fixers['combine_nested_dirname'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['declare_strict_types'], $fixers['blank_line_after_opening_tag']],
            [$fixers['declare_strict_types'], $fixers['declare_equal_normalize']],
            [$fixers['declare_strict_types'], $fixers['header_comment']],
            [$fixers['dir_constant'], $fixers['combine_nested_dirname']],
            [$fixers['doctrine_annotation_array_assignment'], $fixers['doctrine_annotation_spaces']],
            [$fixers['echo_tag_syntax'], $fixers['no_mixed_echo_print']],
            [$fixers['elseif'], $fixers['braces']],
            [$fixers['empty_loop_body'], $fixers['braces']],
            [$fixers['empty_loop_body'], $fixers['no_extra_blank_lines']],
            [$fixers['empty_loop_body'], $fixers['no_trailing_whitespace']],
            [$fixers['escape_implicit_backslashes'], $fixers['heredoc_to_nowdoc']],
            [$fixers['escape_implicit_backslashes'], $fixers['single_quote']],
            [$fixers['explicit_string_variable'], $fixers['simple_to_complex_string_variable']],
            [$fixers['final_internal_class'], $fixers['protected_to_private']],
            [$fixers['final_internal_class'], $fixers['self_static_accessor']],
            [$fixers['fully_qualified_strict_types'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['function_declaration'], $fixers['method_argument_space']],
            [$fixers['function_to_constant'], $fixers['native_function_casing']],
            [$fixers['function_to_constant'], $fixers['no_extra_blank_lines']],
            [$fixers['function_to_constant'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['function_to_constant'], $fixers['no_trailing_whitespace']],
            [$fixers['function_to_constant'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['function_to_constant'], $fixers['self_static_accessor']],
            [$fixers['general_phpdoc_annotation_remove'], $fixers['no_empty_phpdoc']],
            [$fixers['general_phpdoc_annotation_remove'], $fixers['phpdoc_line_span']],
            [$fixers['general_phpdoc_annotation_remove'], $fixers['phpdoc_separation']],
            [$fixers['general_phpdoc_annotation_remove'], $fixers['phpdoc_trim']],
            [$fixers['general_phpdoc_tag_rename'], $fixers['phpdoc_add_missing_param_annotation']],
            [$fixers['global_namespace_import'], $fixers['no_unused_imports']],
            [$fixers['global_namespace_import'], $fixers['ordered_imports']],
            [$fixers['header_comment'], $fixers['single_line_comment_style']],
            [$fixers['implode_call'], $fixers['method_argument_space']],
            [$fixers['indentation_type'], $fixers['phpdoc_indent']],
            [$fixers['is_null'], $fixers['yoda_style']],
            [$fixers['lambda_not_used_import'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['line_ending'], $fixers['braces']],
            [$fixers['list_syntax'], $fixers['binary_operator_spaces']],
            [$fixers['list_syntax'], $fixers['ternary_operator_spaces']],
            [$fixers['method_argument_space'], $fixers['array_indentation']],
            [$fixers['method_chaining_indentation'], $fixers['array_indentation']],
            [$fixers['method_chaining_indentation'], $fixers['method_argument_space']],
            [$fixers['multiline_whitespace_before_semicolons'], $fixers['space_after_semicolon']],
            [$fixers['native_constant_invocation'], $fixers['global_namespace_import']],
            [$fixers['native_function_invocation'], $fixers['global_namespace_import']],
            [$fixers['no_alias_functions'], $fixers['implode_call']],
            [$fixers['no_alias_functions'], $fixers['php_unit_dedicate_assert']],
            [$fixers['no_alternative_syntax'], $fixers['braces']],
            [$fixers['no_alternative_syntax'], $fixers['elseif']],
            [$fixers['no_alternative_syntax'], $fixers['no_superfluous_elseif']],
            [$fixers['no_alternative_syntax'], $fixers['no_useless_else']],
            [$fixers['no_alternative_syntax'], $fixers['switch_continue_to_break']],
            [$fixers['no_blank_lines_after_phpdoc'], $fixers['header_comment']],
            [$fixers['no_empty_comment'], $fixers['no_extra_blank_lines']],
            [$fixers['no_empty_comment'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_comment'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_phpdoc'], $fixers['no_extra_blank_lines']],
            [$fixers['no_empty_phpdoc'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_phpdoc'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_statement'], $fixers['braces']],
            [$fixers['no_empty_statement'], $fixers['combine_consecutive_unsets']],
            [$fixers['no_empty_statement'], $fixers['empty_loop_body']],
            [$fixers['no_empty_statement'], $fixers['multiline_whitespace_before_semicolons']],
            [$fixers['no_empty_statement'], $fixers['no_extra_blank_lines']],
            [$fixers['no_empty_statement'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['no_empty_statement'], $fixers['no_trailing_whitespace']],
            [$fixers['no_empty_statement'], $fixers['no_useless_else']],
            [$fixers['no_empty_statement'], $fixers['no_useless_return']],
            [$fixers['no_empty_statement'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_empty_statement'], $fixers['return_assignment']],
            [$fixers['no_empty_statement'], $fixers['space_after_semicolon']],
            [$fixers['no_empty_statement'], $fixers['switch_case_semicolon_to_colon']],
            [$fixers['no_extra_blank_lines'], $fixers['blank_line_before_statement']],
            [$fixers['no_leading_import_slash'], $fixers['ordered_imports']],
            [$fixers['no_multiline_whitespace_around_double_arrow'], $fixers['binary_operator_spaces']],
            [$fixers['no_multiline_whitespace_around_double_arrow'], $fixers['trailing_comma_in_multiline']],
            [$fixers['no_php4_constructor'], $fixers['ordered_class_elements']],
            [$fixers['no_short_bool_cast'], $fixers['cast_spaces']],
            [$fixers['no_spaces_after_function_name'], $fixers['function_to_constant']],
            [$fixers['no_spaces_inside_parenthesis'], $fixers['function_to_constant']],
            [$fixers['no_superfluous_elseif'], $fixers['simplified_if_return']],
            [$fixers['no_superfluous_phpdoc_tags'], $fixers['no_empty_phpdoc']],
            [$fixers['no_superfluous_phpdoc_tags'], $fixers['void_return']],
            [$fixers['no_unneeded_control_parentheses'], $fixers['no_trailing_whitespace']],
            [$fixers['no_unneeded_curly_braces'], $fixers['no_useless_else']],
            [$fixers['no_unneeded_curly_braces'], $fixers['no_useless_return']],
            [$fixers['no_unneeded_curly_braces'], $fixers['return_assignment']],
            [$fixers['no_unneeded_curly_braces'], $fixers['simplified_if_return']],
            [$fixers['no_unset_cast'], $fixers['binary_operator_spaces']],
            [$fixers['no_unset_on_property'], $fixers['combine_consecutive_unsets']],
            [$fixers['no_unused_imports'], $fixers['blank_line_after_namespace']],
            [$fixers['no_unused_imports'], $fixers['no_extra_blank_lines']],
            [$fixers['no_unused_imports'], $fixers['no_leading_import_slash']],
            [$fixers['no_unused_imports'], $fixers['single_line_after_imports']],
            [$fixers['no_useless_else'], $fixers['braces']],
            [$fixers['no_useless_else'], $fixers['combine_consecutive_unsets']],
            [$fixers['no_useless_else'], $fixers['no_break_comment']],
            [$fixers['no_useless_else'], $fixers['no_extra_blank_lines']],
            [$fixers['no_useless_else'], $fixers['no_trailing_whitespace']],
            [$fixers['no_useless_else'], $fixers['no_useless_return']],
            [$fixers['no_useless_else'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_useless_else'], $fixers['simplified_if_return']],
            [$fixers['no_useless_return'], $fixers['blank_line_before_statement']],
            [$fixers['no_useless_return'], $fixers['no_extra_blank_lines']],
            [$fixers['no_useless_return'], $fixers['no_whitespace_in_blank_line']],
            [$fixers['no_useless_return'], $fixers['single_line_comment_style']],
            [$fixers['no_useless_sprintf'], $fixers['method_argument_space']],
            [$fixers['no_useless_sprintf'], $fixers['native_function_casing']],
            [$fixers['no_useless_sprintf'], $fixers['no_empty_statement']],
            [$fixers['no_useless_sprintf'], $fixers['no_extra_blank_lines']],
            [$fixers['no_useless_sprintf'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['nullable_type_declaration_for_default_null_value'], $fixers['no_unreachable_default_argument_value']],
            [$fixers['ordered_class_elements'], $fixers['class_attributes_separation']],
            [$fixers['ordered_class_elements'], $fixers['no_blank_lines_after_class_opening']],
            [$fixers['ordered_class_elements'], $fixers['space_after_semicolon']],
            [$fixers['php_unit_construct'], $fixers['php_unit_dedicate_assert']],
            [$fixers['php_unit_dedicate_assert'], $fixers['php_unit_dedicate_assert_internal_type']],
            [$fixers['php_unit_fqcn_annotation'], $fixers['no_unused_imports']],
            [$fixers['php_unit_fqcn_annotation'], $fixers['phpdoc_order_by_value']],
            [$fixers['php_unit_internal_class'], $fixers['final_internal_class']],
            [$fixers['php_unit_no_expectation_annotation'], $fixers['no_empty_phpdoc']],
            [$fixers['php_unit_no_expectation_annotation'], $fixers['php_unit_expectation']],
            [$fixers['php_unit_test_annotation'], $fixers['no_empty_phpdoc']],
            [$fixers['php_unit_test_annotation'], $fixers['php_unit_method_casing']],
            [$fixers['php_unit_test_annotation'], $fixers['phpdoc_trim']],
            [$fixers['php_unit_test_case_static_method_calls'], $fixers['self_static_accessor']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_align']],
            [$fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_annotation_without_dot'], $fixers['phpdoc_types']],
            [$fixers['phpdoc_annotation_without_dot'], $fixers['phpdoc_types_order']],
            [$fixers['phpdoc_no_access'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_access'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_access'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_add_missing_param_annotation']],
            [$fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_single_line_var_spacing']],
            [$fixers['phpdoc_no_empty_return'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_package'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_package'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_no_package'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_empty_phpdoc']],
            [$fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_trailing_whitespace_in_comment']],
            [$fixers['phpdoc_order'], $fixers['phpdoc_separation']],
            [$fixers['phpdoc_order'], $fixers['phpdoc_trim']],
            [$fixers['phpdoc_return_self_reference'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['phpdoc_scalar'], $fixers['phpdoc_to_return_type']],
            [$fixers['phpdoc_to_comment'], $fixers['no_empty_comment']],
            [$fixers['phpdoc_to_comment'], $fixers['phpdoc_no_useless_inheritdoc']],
            [$fixers['phpdoc_to_param_type'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['phpdoc_to_property_type'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['phpdoc_to_return_type'], $fixers['fully_qualified_strict_types']],
            [$fixers['phpdoc_to_return_type'], $fixers['no_superfluous_phpdoc_tags']],
            [$fixers['phpdoc_to_return_type'], $fixers['return_type_declaration']],
            [$fixers['phpdoc_types'], $fixers['phpdoc_to_return_type']],
            [$fixers['pow_to_exponentiation'], $fixers['binary_operator_spaces']],
            [$fixers['pow_to_exponentiation'], $fixers['method_argument_space']],
            [$fixers['pow_to_exponentiation'], $fixers['native_function_casing']],
            [$fixers['pow_to_exponentiation'], $fixers['no_spaces_after_function_name']],
            [$fixers['pow_to_exponentiation'], $fixers['no_spaces_inside_parenthesis']],
            [$fixers['protected_to_private'], $fixers['ordered_class_elements']],
            [$fixers['return_assignment'], $fixers['blank_line_before_statement']],
            [$fixers['semicolon_after_instruction'], $fixers['simplified_if_return']],
            [$fixers['simplified_if_return'], $fixers['multiline_whitespace_before_semicolons']],
            [$fixers['simplified_if_return'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['simplified_null_return'], $fixers['no_useless_return']],
            [$fixers['simplified_null_return'], $fixers['void_return']],
            [$fixers['single_class_element_per_statement'], $fixers['class_attributes_separation']],
            [$fixers['single_import_per_statement'], $fixers['multiline_whitespace_before_semicolons']],
            [$fixers['single_import_per_statement'], $fixers['no_leading_import_slash']],
            [$fixers['single_import_per_statement'], $fixers['no_singleline_whitespace_before_semicolons']],
            [$fixers['single_import_per_statement'], $fixers['no_unused_imports']],
            [$fixers['single_import_per_statement'], $fixers['space_after_semicolon']],
            [$fixers['single_line_throw'], $fixers['braces']],
            [$fixers['single_line_throw'], $fixers['concat_space']],
            [$fixers['single_space_after_construct'], $fixers['braces']],
            [$fixers['single_space_after_construct'], $fixers['function_declaration']],
            [$fixers['single_trait_insert_per_statement'], $fixers['braces']],
            [$fixers['single_trait_insert_per_statement'], $fixers['space_after_semicolon']],
            [$fixers['standardize_increment'], $fixers['increment_style']],
            [$fixers['standardize_not_equals'], $fixers['binary_operator_spaces']],
            [$fixers['strict_comparison'], $fixers['binary_operator_spaces']],
            [$fixers['strict_param'], $fixers['native_function_invocation']],
            [$fixers['ternary_to_elvis_operator'], $fixers['no_trailing_whitespace']],
            [$fixers['ternary_to_elvis_operator'], $fixers['ternary_operator_spaces']],
            [$fixers['unary_operator_spaces'], $fixers['not_operator_with_space']],
            [$fixers['unary_operator_spaces'], $fixers['not_operator_with_successor_space']],
            [$fixers['void_return'], $fixers['phpdoc_no_empty_return']],
            [$fixers['void_return'], $fixers['return_type_declaration']],
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

        // Prepare bulk tests for phpdoc fixers to test that:
        // * `align_multiline_comment` is first
        // * `comment_to_phpdoc` is second
        // * `phpdoc_to_comment` is third
        // * `phpdoc_indent` is fourth
        // * `phpdoc_types` is fifth
        // * `phpdoc_scalar` is sixth
        // * `phpdoc_align` is last
        // Add these cases in test-order instead of alphabetical
        $cases[] = [$fixers['align_multiline_comment'], $fixers['comment_to_phpdoc']];
        $cases[] = [$fixers['comment_to_phpdoc'], $fixers['phpdoc_to_comment']];
        $cases[] = [$fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']];
        $cases[] = [$fixers['phpdoc_indent'], $fixers['phpdoc_types']];
        $cases[] = [$fixers['phpdoc_types'], $fixers['phpdoc_scalar']];

        $docFixerNames = array_filter(
            array_keys($fixers),
            static function (string $name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!\in_array($docFixerName, ['comment_to_phpdoc', 'phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'], true)) {
                $cases[] = [$fixers['align_multiline_comment'], $fixers[$docFixerName]];
                $cases[] = [$fixers['comment_to_phpdoc'], $fixers[$docFixerName]];
                $cases[] = [$fixers['phpdoc_indent'], $fixers[$docFixerName]];
                $cases[] = [$fixers['phpdoc_to_comment'], $fixers[$docFixerName]];

                if ('phpdoc_annotation_without_dot' !== $docFixerName) {
                    $cases[] = [$fixers['phpdoc_scalar'], $fixers[$docFixerName]];
                    $cases[] = [$fixers['phpdoc_types'], $fixers[$docFixerName]];
                }
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
    public function testFixersPriorityPairsHaveIntegrationTest(FixerInterface $first, FixerInterface $second): void
    {
        $integrationTestName = $this->generateIntegrationTestName($first, $second);
        $file = $this->getIntegrationPriorityDirectory().$integrationTestName;

        if (is_file($file)) {
            $description = sprintf('Integration of fixers: %s,%s.', $first->getName(), $second->getName());
            $integrationTestExists = true;
        } else {
            $file = $this->getIntegrationPriorityDirectory().$this->generateIntegrationTestName($second, $first);
            $description = sprintf('Integration of fixers: %s,%s.', $second->getName(), $first->getName());
            $integrationTestExists = is_file($file);
        }

        static::assertTrue($integrationTestExists, sprintf('There shall be an integration test "%s". How do you know that priority set up is good, if there is no integration test to check it?', $integrationTestName));

        $file = realpath($file);
        $factory = new IntegrationCaseFactory();

        $test = $factory->create(new SplFileInfo($file, './', __DIR__));
        $rules = $test->getRuleset()->getRules();
        $expected = [$first->getName(), $second->getName()];
        $actual = array_keys($rules);

        sort($expected);
        sort($actual);

        static::assertSame($description, $test->getTitle(), sprintf('Please fix the title in "%s".', $file));
        static::assertCount(2, $rules, sprintf('Only the two rules that are tested for priority should be in the ruleset of "%s".', $file));

        foreach ($rules as $name => $config) {
            static::assertNotFalse($config, sprintf('The rule "%s" in "%s" may not be disabled for the test.', $name, $file));
        }

        static::assertSame($expected, $actual, sprintf('The ruleset of "%s" must contain the rules for the priority test.', $file));
    }

    public function provideFixersPriorityPairsHaveIntegrationTestCases(): array
    {
        return array_filter(
            $this->provideFixersPriorityCases(),
            // ignore speed-up only priorities set up
            function (array $case) {
                return !\in_array(
                    $this->generateIntegrationTestName($case[0], $case[1]),
                    [
                        'function_to_constant,native_function_casing.test',
                        'no_unused_imports,no_leading_import_slash.test',
                        'pow_to_exponentiation,method_argument_space.test',
                        'pow_to_exponentiation,native_function_casing.test',
                        'pow_to_exponentiation,no_spaces_after_function_name.test',
                        'pow_to_exponentiation,no_spaces_inside_parenthesis.test',
                        'no_useless_sprintf,native_function_casing.test',
                    ],
                    true
                );
            }
        );
    }

    public function testPriorityIntegrationDirectoryOnlyContainsFiles(): void
    {
        foreach (new \DirectoryIterator($this->getIntegrationPriorityDirectory()) as $candidate) {
            if ($candidate->isDot()) {
                continue;
            }

            $fileName = $candidate->getFilename();
            static::assertTrue($candidate->isFile(), sprintf('Expected only files in the priority integration test directory, got "%s".', $fileName));
            static::assertFalse($candidate->isLink(), sprintf('No (sym)links expected the priority integration test directory, got "%s".', $fileName));
        }
    }

    /**
     * @dataProvider provideIntegrationTestFilesCases
     */
    public function testPriorityIntegrationTestFilesAreListedPriorityCases(string $fileName): void
    {
        static $priorityCases;

        if (null === $priorityCases) {
            $priorityCases = [];

            foreach ($this->provideFixersPriorityCases() as $priorityCase) {
                $fixerName = $priorityCase[0]->getName();
                if (!isset($priorityCases[$fixerName])) {
                    $priorityCases[$fixerName] = [];
                }

                $priorityCases[$fixerName][$priorityCase[1]->getName()] = true;
            }

            ksort($priorityCases);
        }

        static::assertSame(
            1,
            preg_match('#^([a-z][a-z0-9_]*),([a-z][a-z_]*)(?:_\d{1,3})?\.test(-(in|out)\.php)?$#', $fileName, $matches),
            sprintf('File with unexpected name "%s" in the priority integration test directory.', $fileName)
        );

        $fixerName1 = $matches[1];
        $fixerName2 = $matches[2];

        static::assertTrue(
            isset($priorityCases[$fixerName1][$fixerName2]),
            sprintf('Missing priority test entry for file "%s".', $fileName)
        );
    }

    public function provideIntegrationTestFilesCases(): array
    {
        $fileNames = [];

        foreach (new \DirectoryIterator($this->getIntegrationPriorityDirectory()) as $candidate) {
            if ($candidate->isDot()) {
                continue;
            }

            $fileNames[] = [$candidate->getFilename()];
        }

        sort($fileNames);

        return $fileNames;
    }

    public function testProvideFixersPriorityCasesAreSorted(): void
    {
        $cases = $this->provideFixersPriorityCases();
        $sorted = $cases;

        usort(
            $sorted,
            /**
             * @param array<FixerInterface> $priorityPair1
             * @param array<FixerInterface> $priorityPair2
             */
            static function (array $priorityPair1, array $priorityPair2) {
                $fixer1 = $priorityPair1[0];
                $fixer2 = $priorityPair2[0];

                if ($fixer1->getName() === $fixer2->getName()) {
                    $fixer1 = $priorityPair1[1];
                    $fixer2 = $priorityPair2[1];
                }

                return strcmp($fixer1->getName(), $fixer2->getName());
            }
        );

        if ($sorted !== $cases) { // PHPUnit takes a very long time creating a diff view on the arrays
            $casesDescription = '';

            foreach ($cases as $pair) {
                $casesDescription .= sprintf("\n%s/%s", $pair[0]->getName(), $pair[1]->getName());
            }

            $sortedDescription = '';

            foreach ($sorted as $pair) {
                $sortedDescription .= sprintf("\n%s/%s", $pair[0]->getName(), $pair[1]->getName());
            }

            static::assertSame($sortedDescription, $casesDescription);
        } else {
            $this->addToAssertionCount(1);
        }
    }

    public function testFixerPriorityComment(): void
    {
        $cases = array_merge(
            $this->provideFixersPriorityCases(),
            $this->provideFixersPrioritySpecialPhpdocCases()
        );

        $map = [];

        foreach ($cases as $beforeAfter) {
            [$before, $after] = $beforeAfter;

            $beforeClass = \get_class($before);
            $afterClass = \get_class($after);

            $beforeName = substr($beforeClass, strrpos($beforeClass, '\\') + 1);
            $afterName = substr($afterClass, strrpos($afterClass, '\\') + 1);

            if (!isset($map[$beforeName])) {
                $map[$beforeName] = [
                    'before' => [],
                    'after' => [],
                    'class' => $beforeClass,
                ];
            }

            $map[$beforeName]['before'][] = $afterName;

            if (!isset($map[$afterName])) {
                $map[$afterName] = [
                    'before' => [],
                    'after' => [],
                    'class' => $afterClass,
                ];
            }

            $map[$afterName]['after'][] = $beforeName;
        }

        $fixersPhpDocIssues = [];

        foreach ($map as $fixerName => $priorityMap) {
            $expectedMessage = "/**\n     * {@inheritdoc}\n     *";

            if (\count($priorityMap['before']) > 0) {
                sort($priorityMap['before']);
                $expectedMessage .= sprintf("\n     * Must run before %s.", implode(', ', $priorityMap['before']));
            }

            // @phpstan-ignore-next-line to avoid `Comparison operation ">" between int<1, max> and 0 is always true.`
            if (\count($priorityMap['after']) > 0) {
                sort($priorityMap['after']);
                $expectedMessage .= sprintf("\n     * Must run after %s.", implode(', ', $priorityMap['after']));
            }

            $expectedMessage .= "\n     */";

            $reflection = new \ReflectionClass($priorityMap['class']);
            $method = $reflection->getMethod('getPriority');
            $phpDoc = $method->getDocComment();

            if (false === $phpDoc) {
                $fixersPhpDocIssues[$fixerName] = sprintf("PHPDoc for %s::getPriority is missing.\nExpected:\n%s", $fixerName, $expectedMessage);

                continue;
            }

            if ($expectedMessage !== $phpDoc) {
                $fixersPhpDocIssues[$fixerName] = sprintf("PHPDoc for %s::getPriority is not as expected.\nExpected:\n%s", $fixerName, $expectedMessage);

                continue;
            }
        }

        if (0 === \count($fixersPhpDocIssues)) {
            $this->addToAssertionCount(1);
        } else {
            $message = sprintf("There are %d priority PHPDoc issues found.\n", \count($fixersPhpDocIssues));

            ksort($fixersPhpDocIssues);
            foreach ($fixersPhpDocIssues as $fixerName => $issue) {
                $message .= sprintf("\n--------------------------------------------------\n%s\n%s", $fixerName, $issue);
            }

            static::fail($message);
        }
    }

    private function generateIntegrationTestName(FixerInterface $first, FixerInterface $second): string
    {
        return "{$first->getName()},{$second->getName()}.test";
    }

    private function getIntegrationPriorityDirectory(): string
    {
        return __DIR__.'/../Fixtures/Integration/priority/';
    }
}
