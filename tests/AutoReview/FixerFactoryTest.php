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

        $this->assertSame('encoding', $fixers[0]->getName());
        $this->assertSame('full_opening_tag', $fixers[1]->getName());
        $this->assertSame('single_blank_line_at_eof', $fixers[count($fixers) - 1]->getName());
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

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        return array(
            array($fixers['array_syntax'], $fixers['binary_operator_spaces']),
            array($fixers['array_syntax'], $fixers['ternary_operator_spaces']),
            array($fixers['blank_line_after_opening_tag'], $fixers['no_blank_lines_before_namespace']),
            array($fixers['class_keyword_remove'], $fixers['no_unused_imports']),
            array($fixers['combine_consecutive_unsets'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['combine_consecutive_unsets'], $fixers['no_trailing_whitespace']),
            array($fixers['combine_consecutive_unsets'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['combine_consecutive_unsets'], $fixers['space_after_semicolon']),
            array($fixers['declare_strict_types'], $fixers['blank_line_after_opening_tag']),
            array($fixers['declare_strict_types'], $fixers['declare_equal_normalize']),
            array($fixers['declare_strict_types'], $fixers['single_blank_line_before_namespace']),
            array($fixers['elseif'], $fixers['braces']),
            array($fixers['function_to_constant'], $fixers['native_function_casing']),
            array($fixers['function_to_constant'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['function_to_constant'], $fixers['no_singleline_whitespace_before_semicolons']),
            array($fixers['function_to_constant'], $fixers['no_trailing_whitespace']),
            array($fixers['function_to_constant'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['indentation_type'], $fixers['phpdoc_indent']),
            array($fixers['line_ending'], $fixers['single_blank_line_at_eof']),
            array($fixers['method_separation'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['indentation_type']),
            array($fixers['no_alias_functions'], $fixers['php_unit_dedicate_assert']),
            array($fixers['no_blank_lines_after_phpdoc'], $fixers['single_blank_line_before_namespace']),
            array($fixers['no_empty_comment'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_empty_comment'], $fixers['no_trailing_whitespace']),
            array($fixers['no_empty_comment'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['no_empty_phpdoc'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_empty_phpdoc'], $fixers['no_trailing_whitespace']),
            array($fixers['no_empty_phpdoc'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['no_empty_statement'], $fixers['braces']),
            array($fixers['no_empty_statement'], $fixers['combine_consecutive_unsets']),
            array($fixers['no_empty_statement'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_empty_statement'], $fixers['no_multiline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_singleline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_trailing_whitespace']),
            array($fixers['no_empty_statement'], $fixers['no_useless_else']),
            array($fixers['no_empty_statement'], $fixers['no_useless_return']),
            array($fixers['no_empty_statement'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['no_empty_statement'], $fixers['space_after_semicolon']),
            array($fixers['no_empty_statement'], $fixers['switch_case_semicolon_to_colon']),
            array($fixers['no_leading_import_slash'], $fixers['ordered_imports']),
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['binary_operator_spaces']),
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['trailing_comma_in_multiline_array']),
            array($fixers['no_multiline_whitespace_before_semicolons'], $fixers['space_after_semicolon']),
            array($fixers['no_php4_constructor'], $fixers['ordered_class_elements']),
            array($fixers['no_short_bool_cast'], $fixers['cast_spaces']),
            array($fixers['no_short_echo_tag'], $fixers['no_mixed_echo_print']),
            array($fixers['no_spaces_after_function_name'], $fixers['function_to_constant']),
            array($fixers['no_spaces_inside_parenthesis'], $fixers['function_to_constant']),
            array($fixers['no_unneeded_control_parentheses'], $fixers['no_trailing_whitespace']),
            array($fixers['no_unused_imports'], $fixers['blank_line_after_namespace']),
            array($fixers['no_unused_imports'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_unused_imports'], $fixers['no_leading_import_slash']),
            array($fixers['no_useless_else'], $fixers['braces']),
            array($fixers['no_useless_else'], $fixers['combine_consecutive_unsets']),
            array($fixers['no_useless_else'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_useless_else'], $fixers['no_trailing_whitespace']),
            array($fixers['no_useless_else'], $fixers['no_useless_return']),
            array($fixers['no_useless_else'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['no_useless_return'], $fixers['blank_line_before_return']),
            array($fixers['no_useless_return'], $fixers['no_extra_consecutive_blank_lines']),
            array($fixers['no_useless_return'], $fixers['no_whitespace_in_blank_line']),
            array($fixers['ordered_class_elements'], $fixers['method_separation']),
            array($fixers['ordered_class_elements'], $fixers['no_blank_lines_after_class_opening']),
            array($fixers['ordered_class_elements'], $fixers['space_after_semicolon']),
            array($fixers['php_unit_fqcn_annotation'], $fixers['no_unused_imports']),
            array($fixers['php_unit_strict'], $fixers['php_unit_construct']),
            array($fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_align']),
            array($fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['no_empty_phpdoc']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_add_missing_param_annotation']),
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_single_line_var_spacing']),
            array($fixers['phpdoc_no_empty_return'], $fixers['no_empty_phpdoc']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_package'], $fixers['no_empty_phpdoc']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_empty_phpdoc']),
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_trailing_whitespace_in_comment']),
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['phpdoc_inline_tag']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_separation'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_summary'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_to_comment'], $fixers['no_empty_comment']),
            array($fixers['phpdoc_to_comment'], $fixers['phpdoc_no_useless_inheritdoc']),
            array($fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']),
            array($fixers['pow_to_exponentiation'], $fixers['binary_operator_spaces']),
            array($fixers['pow_to_exponentiation'], $fixers['method_argument_space']),
            array($fixers['pow_to_exponentiation'], $fixers['native_function_casing']),
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_after_function_name']),
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_inside_parenthesis']),
            array($fixers['protected_to_private'], $fixers['ordered_class_elements']),
            array($fixers['simplified_null_return'], $fixers['no_useless_return']),
            array($fixers['single_import_per_statement'], $fixers['no_leading_import_slash']),
            array($fixers['single_import_per_statement'], $fixers['no_multiline_whitespace_before_semicolons']),
            array($fixers['single_import_per_statement'], $fixers['no_singleline_whitespace_before_semicolons']),
            array($fixers['single_import_per_statement'], $fixers['no_unused_imports']),
            array($fixers['single_import_per_statement'], $fixers['ordered_imports']),
            array($fixers['single_import_per_statement'], $fixers['space_after_semicolon']),
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_space']),
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_successor_space']),
        );
    }

    public function provideFixersPrioritySpecialPhpdocCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $cases = array();

        // prepare bulk tests for phpdoc fixers to test that:
        // * `phpdoc_to_comment` is first
        // * `phpdoc_indent` is second
        // * `phpdoc_types` is third
        // * `phpdoc_scalar` is fourth
        // * `phpdoc_align` is last
        $cases[] = array($fixers['phpdoc_indent'], $fixers['phpdoc_types']);
        $cases[] = array($fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']);
        $cases[] = array($fixers['phpdoc_types'], $fixers['phpdoc_scalar']);

        $docFixerNames = array_filter(
            array_keys($fixers),
            function ($name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!in_array($docFixerName, array('phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'), true)) {
                $cases[] = array($fixers['phpdoc_indent'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_scalar'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_to_comment'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_types'], $fixers[$docFixerName]);
            }

            if ('phpdoc_align' !== $docFixerName) {
                $cases[] = array($fixers[$docFixerName], $fixers['phpdoc_align']);
            }
        }

        return $cases;
    }

    /**
     * @dataProvider provideFixersPriorityPairsHaveIntegrationTestCases
     *
     * @requires PHP 5.4
     */
    public function testFixersPriorityPairsHaveIntegrationTest(FixerInterface $first, FixerInterface $second)
    {
        // This structure contains older cases that are not yet covered by tests.
        // It may only shrink, never add anything to it.
        $casesWithoutTests = array(
            'elseif,braces.test',
            'indentation_type,phpdoc_indent.test',
            'line_ending,single_blank_line_at_eof.test',
            'method_separation,braces.test',
            'method_separation,indentation_type.test',
            'no_empty_statement,braces.test',
            'no_empty_statement,no_multiline_whitespace_before_semicolons.test',
            'no_empty_statement,no_singleline_whitespace_before_semicolons.test',
            'no_useless_else,braces.test',
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
        );

        $integrationTestExists = $this->doesIntegrationTestExist($first, $second);

        if (in_array($this->generateIntegrationTestName($first, $second), $casesWithoutTests, true)) {
            $this->assertFalse($integrationTestExists, sprintf('Case "%s" already has an integration test, so it should be removed from "$casesWithoutTests".', $this->generateIntegrationTestName($first, $second)));
            $this->markTestIncomplete(sprintf('Case "%s" has no integration test yet, please help and add it.', $this->generateIntegrationTestName($first, $second)));
        }

        $this->assertTrue($integrationTestExists, sprintf('There shall be an integration test "%s". How do you know that priority set up is good, if there is no integration test to check it?', $this->generateIntegrationTestName($first, $second)));
    }

    public function provideFixersPriorityPairsHaveIntegrationTestCases()
    {
        $self = $this;

        return array_filter(
            $this->provideFixersPriorityCases(),
            // ignore speed-up only priorities set up
            function (array $case) use ($self) {
                return !in_array(
                    $self->generateIntegrationTestName($case[0], $case[1]),
                    array(
                        'function_to_constant,native_function_casing.test',
                        'no_unused_imports,no_leading_import_slash.test',
                        'pow_to_exponentiation,method_argument_space.test',
                        'pow_to_exponentiation,native_function_casing.test',
                        'pow_to_exponentiation,no_spaces_after_function_name.test',
                        'pow_to_exponentiation,no_spaces_inside_parenthesis.test',
                    ),
                    true
                );
            }
        );
    }

    /**
     * @private
     */
    public function generateIntegrationTestName(FixerInterface $first, FixerInterface $second)
    {
        return "{$first->getName()},{$second->getName()}.test";
    }

    private function doesIntegrationTestExist(FixerInterface $first, FixerInterface $second)
    {
        return is_file(__DIR__.'/../Fixtures/Integration/priority/'.$this->generateIntegrationTestName($first, $second)) || is_file(__DIR__.'/../Fixtures/Integration/priority/'.$this->generateIntegrationTestName($second, $first));
    }
}
