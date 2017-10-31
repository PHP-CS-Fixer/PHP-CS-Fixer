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
use PHPUnit\Framework\TestCase;

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
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority());
    }

    public function provideFixersPriorityCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $cases = array(
            array($fixers['elseif'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['indentation_type']),
            array($fixers['no_leading_import_slash'], $fixers['ordered_imports']), // tested also in: no_leading_import_slash,ordered_imports.test
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['binary_operator_spaces']), // tested also in: no_multiline_whitespace_around_double_arrow,binary_operator_spaces.test
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['trailing_comma_in_multiline_array']), // tested also in: no_multiline_whitespace_around_double_arrow,trailing_comma_in_multiline_array.test
            array($fixers['no_php4_constructor'], $fixers['ordered_class_elements']), // tested also in: no_php4_constructor,ordered_class_elements.test
            array($fixers['no_short_bool_cast'], $fixers['cast_spaces']), // tested also in: no_short_bool_cast,cast_spaces.test
            array($fixers['no_short_echo_tag'], $fixers['no_mixed_echo_print']), // tested also in: no_mixed_echo_print,no_short_echo_tag.test
            array($fixers['indentation_type'], $fixers['phpdoc_indent']),
            array($fixers['no_unneeded_control_parentheses'], $fixers['no_trailing_whitespace']), // tested also in: no_trailing_whitespace,no_unneeded_control_parentheses.test
            array($fixers['no_unused_imports'], $fixers['blank_line_after_namespace']), // tested also in: no_unused_imports,blank_line_after_namespace.test and no_unused_imports,blank_line_after_namespace_2.test
            array($fixers['no_unused_imports'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_unused_imports,no_extra_consecutive_blank_lines.test
            array($fixers['no_unused_imports'], $fixers['no_leading_import_slash']), // no priority issue; for speed only
            array($fixers['ordered_class_elements'], $fixers['method_separation']), // tested also in: ordered_class_elements,method_separation.test
            array($fixers['ordered_class_elements'], $fixers['no_blank_lines_after_class_opening']), // tested also in: ordered_class_elements,no_blank_lines_after_class_opening.test
            array($fixers['ordered_class_elements'], $fixers['space_after_semicolon']), // tested also in: ordered_class_elements,space_after_semicolon.test
            array($fixers['php_unit_strict'], $fixers['php_unit_construct']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']), // tested also in: phpdoc_no_empty_return,phpdoc_separation.test
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']), // tested also in: phpdoc_no_empty_return,phpdoc_separation.test
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_separation']), // tested also in: phpdoc_no_package,phpdoc_separation.test
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_separation'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_summary'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']),
            array($fixers['pow_to_exponentiation'], $fixers['binary_operator_spaces']), // tested also in: pow_to_exponentiation,binary_operator_spaces.test
            array($fixers['pow_to_exponentiation'], $fixers['method_argument_space']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['native_function_casing']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_after_function_name']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_inside_parenthesis']), // no priority issue; for speed only
            array($fixers['single_import_per_statement'], $fixers['ordered_imports']), // tested also in: single_import_per_statement,ordered_imports.test
            array($fixers['single_import_per_statement'], $fixers['no_singleline_whitespace_before_semicolons']), // tested also in: single_import_per_statement,no_singleline_whitespace_before_semicolons.test
            array($fixers['single_import_per_statement'], $fixers['space_after_semicolon']), // tested also in: single_import_per_statement,space_after_semicolon.test
            array($fixers['single_import_per_statement'], $fixers['no_multiline_whitespace_before_semicolons']), // single_import_per_statement,no_multiline_whitespace_before_semicolons.test
            array($fixers['single_import_per_statement'], $fixers['no_leading_import_slash']), // tested also in: single_import_per_statement,no_leading_import_slash.test
            array($fixers['single_import_per_statement'], $fixers['no_unused_imports']), // tested also in: single_import_per_statement,no_unused_imports.test
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_space']),
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_successor_space']),
            array($fixers['line_ending'], $fixers['single_blank_line_at_eof']),
            array($fixers['simplified_null_return'], $fixers['no_useless_return']), // tested also in: simplified_null_return,no_useless_return.test
            array($fixers['no_useless_return'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_useless_return,no_whitespace_in_blank_line.test
            array($fixers['no_useless_return'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_useless_return,no_extra_consecutive_blank_lines.test
            array($fixers['no_useless_return'], $fixers['blank_line_before_return']), // tested also in: no_useless_return,blank_line_before_return.test
            array($fixers['no_empty_phpdoc'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_phpdoc,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_phpdoc'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_phpdoc,no_trailing_whitespace.test
            array($fixers['no_empty_phpdoc'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_phpdoc,no_whitespace_in_blank_line.test
            array($fixers['phpdoc_no_access'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_access,no_empty_phpdoc.test
            array($fixers['phpdoc_no_empty_return'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_empty_return,no_empty_phpdoc.test
            array($fixers['phpdoc_no_package'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_package,no_empty_phpdoc.test
            array($fixers['combine_consecutive_unsets'], $fixers['space_after_semicolon']), // tested also in: combine_consecutive_unsets,space_after_semicolon.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_whitespace_in_blank_line']), // tested also in: combine_consecutive_unsets,no_whitespace_in_blank_line.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_trailing_whitespace']), // tested also in: combine_consecutive_unsets,no_trailing_whitespace.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: combine_consecutive_unsets,no_extra_consecutive_blank_lines.test
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_single_line_var_spacing']), // tested also in: phpdoc_no_alias_tag,phpdoc_single_line_var_spacing.test
            array($fixers['blank_line_after_opening_tag'], $fixers['no_blank_lines_before_namespace']), // tested also in: blank_line_after_opening_tag,no_blank_lines_before_namespace.test
            array($fixers['phpdoc_to_comment'], $fixers['no_empty_comment']), // tested also in: phpdoc_to_comment,no_empty_comment.test
            array($fixers['no_empty_comment'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_comment,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_comment'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_comment,no_trailing_whitespace.test
            array($fixers['no_empty_comment'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_comment,no_whitespace_in_blank_line.test
            array($fixers['no_alias_functions'], $fixers['php_unit_dedicate_assert']), // tested also in: no_alias_functions,php_unit_dedicate_assert.test
            array($fixers['no_empty_statement'], $fixers['braces']),
            array($fixers['no_empty_statement'], $fixers['combine_consecutive_unsets']), // tested also in: no_empty_statement,combine_consecutive_unsets.test
            array($fixers['no_empty_statement'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_statement,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_statement'], $fixers['no_multiline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_singleline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_statement,no_trailing_whitespace.test
            array($fixers['no_empty_statement'], $fixers['no_useless_else']), // tested also in: no_empty_statement,no_useless_else.test
            array($fixers['no_empty_statement'], $fixers['no_useless_return']), // tested also in: no_empty_statement,no_useless_return.test
            array($fixers['no_empty_statement'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_statement,no_whitespace_in_blank_line.test
            array($fixers['no_empty_statement'], $fixers['space_after_semicolon']), // tested also in: no_empty_statement,space_after_semicolon.test
            array($fixers['no_empty_statement'], $fixers['switch_case_semicolon_to_colon']), // tested also in: no_empty_statement,switch_case_semicolon_to_colon.test
            array($fixers['no_useless_else'], $fixers['braces']),
            array($fixers['no_useless_else'], $fixers['combine_consecutive_unsets']), // tested also in: no_useless_else,combine_consecutive_unsets.test
            array($fixers['no_useless_else'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_useless_else,no_extra_consecutive_blank_lines.test
            array($fixers['no_useless_else'], $fixers['no_useless_return']), // tested also in: no_useless_else,no_useless_return.test
            array($fixers['no_useless_else'], $fixers['no_trailing_whitespace']), // tested also in: no_useless_else,no_trailing_whitespace.test
            array($fixers['no_useless_else'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_useless_else,no_whitespace_in_blank_line.test
            array($fixers['declare_strict_types'], $fixers['single_blank_line_before_namespace']), // tested also in: declare_strict_types,single_blank_line_before_namespace.test
            array($fixers['declare_strict_types'], $fixers['blank_line_after_opening_tag']), // tested also in: declare_strict_types,blank_line_after_opening_tag.test
            array($fixers['array_syntax'], $fixers['binary_operator_spaces']), // tested also in: array_syntax,binary_operator_spaces.test
            array($fixers['array_syntax'], $fixers['ternary_operator_spaces']), // tested also in: array_syntax,ternary_operator_spaces.test
            array($fixers['class_keyword_remove'], $fixers['no_unused_imports']), // tested also in: class_keyword_remove,no_unused_imports.test
            array($fixers['no_blank_lines_after_phpdoc'], $fixers['single_blank_line_before_namespace']), // tested also in: no_blank_lines_after_phpdoc,single_blank_line_before_namespace.test
            array($fixers['php_unit_fqcn_annotation'], $fixers['no_unused_imports']), // tested also in: php_unit_fqcn_annotation,unused_use.test
            array($fixers['protected_to_private'], $fixers['ordered_class_elements']), // tested also in: protected_to_private,ordered_class_elements.test
            array($fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_align']), // tested also in: phpdoc_add_missing_param_annotation,phpdoc_align.test
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_add_missing_param_annotation']), // tested also in: phpdoc_no_alias_tag,phpdoc_add_missing_param_annotation.test
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_useless_inheritdoc,no_empty_phpdoc.test
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['no_trailing_whitespace_in_comment']), // tested also in: phpdoc_no_useless_inheritdoc,no_trailing_whitespace_in_comment.test
            array($fixers['phpdoc_no_useless_inheritdoc'], $fixers['phpdoc_inline_tag']), // tested also in: phpdoc_no_useless_inheritdoc,phpdoc_inline_tag.test
            array($fixers['phpdoc_to_comment'], $fixers['phpdoc_no_useless_inheritdoc']), // tested also in: phpdoc_to_comment,phpdoc_no_useless_inheritdoc.test
            array($fixers['declare_strict_types'], $fixers['declare_equal_normalize']), // tested also in: declare_strict_types,declare_equal_normalize.test
            array($fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_order']), // tested also in: phpdoc_add_missing_param_annotation,phpdoc_order.test
            array($fixers['no_spaces_after_function_name'], $fixers['function_to_constant']), // tested also in: no_spaces_after_function_name,function_to_constant.test
            array($fixers['no_spaces_inside_parenthesis'], $fixers['function_to_constant']), // tested also in: no_spaces_inside_parenthesis,function_to_constant.test
            array($fixers['function_to_constant'], $fixers['native_function_casing']), // no priority issue; for speed only
            array($fixers['function_to_constant'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: function_to_constant,no_extra_consecutive_blank_lines.test
            array($fixers['function_to_constant'], $fixers['no_singleline_whitespace_before_semicolons']), // tested also in: function_to_constant,no_singleline_whitespace_before_semicolons.test
            array($fixers['function_to_constant'], $fixers['no_trailing_whitespace']), // tested also in: function_to_constant,no_trailing_whitespace.test
            array($fixers['function_to_constant'], $fixers['no_whitespace_in_blank_line']), // tested also in: function_to_constant,no_whitespace_in_blank_line.test
            array($fixers['no_multiline_whitespace_before_semicolons'], $fixers['space_after_semicolon']), // tested also in: tests/Fixtures/Integration/priority/no_multiline_whitespace_before_semicolons,space_after_semicolon.test
        );

        // prepare bulk tests for phpdoc fixers to test that:
        // * `phpdoc_to_comment` is first
        // * `phpdoc_indent` is second
        // * `phpdoc_types` is third
        // * `phpdoc_scalar` is fourth
        // * `phpdoc_align` is last
        $cases[] = array($fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']);
        $cases[] = array($fixers['phpdoc_indent'], $fixers['phpdoc_types']);
        $cases[] = array($fixers['phpdoc_types'], $fixers['phpdoc_scalar']);

        $docFixerNames = array_filter(
            array_keys($fixers),
            function ($name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!in_array($docFixerName, array('phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'), true)) {
                $cases[] = array($fixers['phpdoc_to_comment'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_indent'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_types'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_scalar'], $fixers[$docFixerName]);
            }

            if ('phpdoc_align' !== $docFixerName) {
                $cases[] = array($fixers[$docFixerName], $fixers['phpdoc_align']);
            }
        }

        return $cases;
    }
}
