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

namespace PhpCsFixer\RuleSet\Sets;

use PhpCsFixer\RuleSet\AbstractRuleSetDescription;

/**
 * @internal
 */
final class SymfonySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PSR12' => true,
            'array_syntax' => true,
            'backtick_to_shell_exec' => true,
            'binary_operator_spaces' => true,
            'blank_line_before_statement' => [
                'statements' => [
                    'return',
                ],
            ],
            'braces' => [
                'allow_single_line_anonymous_class_with_empty_body' => true,
                'allow_single_line_closure' => true,
            ],
            'cast_spaces' => true,
            'class_attributes_separation' => [
                'elements' => [
                    'method' => 'one',
                ],
            ],
            'class_definition' => [
                'single_line' => true,
            ],
            'clean_namespace' => true,
            'concat_space' => true,
            'echo_tag_syntax' => true,
            'empty_loop_body' => ['style' => 'braces'],
            'empty_loop_condition' => true,
            'fully_qualified_strict_types' => true,
            'function_typehint_space' => true,
            'general_phpdoc_tag_rename' => [
                'replacements' => [
                    'inheritDocs' => 'inheritDoc',
                ],
            ],
            'include' => true,
            'increment_style' => true,
            'integer_literal_case' => true,
            'lambda_not_used_import' => true,
            'linebreak_after_opening_tag' => true,
            'magic_constant_casing' => true,
            'magic_method_casing' => true,
            'method_argument_space' => [
                'on_multiline' => 'ignore',
            ],
            'native_function_casing' => true,
            'native_function_type_declaration_casing' => true,
            'no_alias_language_construct_call' => true,
            'no_alternative_syntax' => true,
            'no_binary_string' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_blank_lines' => [
                'tokens' => [
                    'case',
                    'continue',
                    'curly_brace_block',
                    'default',
                    'extra',
                    'parenthesis_brace_block',
                    'square_brace_block',
                    'switch',
                    'throw',
                    'use',
                ],
            ],
            'no_leading_namespace_whitespace' => true,
            'no_mixed_echo_print' => true,
            'no_multiline_whitespace_around_double_arrow' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_spaces_around_offset' => true,
            'no_superfluous_phpdoc_tags' => [
                'allow_mixed' => true,
                'allow_unused_params' => true,
            ],
            'no_trailing_comma_in_list_call' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'no_unneeded_control_parentheses' => [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                ],
            ],
            'no_unneeded_curly_braces' => [
                'namespaces' => true,
            ],
            'no_unset_cast' => true,
            'no_unused_imports' => true,
            'no_whitespace_before_comma_in_array' => true,
            'normalize_index_brace' => true,
            'object_operator_without_whitespace' => true,
            'ordered_imports' => true,
            'php_unit_fqcn_annotation' => true,
            'php_unit_method_casing' => true,
            'phpdoc_align' => true,
            'phpdoc_annotation_without_dot' => true,
            'phpdoc_indent' => true,
            'phpdoc_inline_tag_normalizer' => true,
            'phpdoc_no_access' => true,
            'phpdoc_no_alias_tag' => true,
            'phpdoc_no_package' => true,
            'phpdoc_no_useless_inheritdoc' => true,
            'phpdoc_return_self_reference' => true,
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_single_line_var_spacing' => true,
            'phpdoc_summary' => true,
            'phpdoc_tag_type' => [
                'tags' => [
                    'inheritDoc' => 'inline',
                ],
            ],
            'phpdoc_to_comment' => true,
            'phpdoc_trim' => true,
            'phpdoc_trim_consecutive_blank_line_separation' => true,
            'phpdoc_types' => true,
            'phpdoc_types_order' => [
                'null_adjustment' => 'always_last',
                'sort_algorithm' => 'none',
            ],
            'phpdoc_var_without_name' => true,
            'protected_to_private' => true,
            'semicolon_after_instruction' => true,
            'single_class_element_per_statement' => true,
            'single_line_comment_style' => [
                'comment_types' => [
                    'hash',
                ],
            ],
            'single_line_throw' => true,
            'single_quote' => true,
            'single_space_after_construct' => true,
            'space_after_semicolon' => [
                'remove_in_empty_for_expressions' => true,
            ],
            'standardize_increment' => true,
            'standardize_not_equals' => true,
            'switch_continue_to_break' => true,
            'trailing_comma_in_multiline' => true,
            'trim_array_spaces' => true,
            'types_spaces' => true,
            'unary_operator_spaces' => true,
            'whitespace_after_comma_in_array' => true,
            'yoda_style' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.';
    }
}
