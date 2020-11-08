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

namespace PhpCsFixer\RuleSet\Sets;

use PhpCsFixer\RuleSet\AbstractRuleSetDescription;

/**
 * @internal
 */
final class PhpCsFixerSet extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return [
            '@Symfony' => true,
            'align_multiline_comment' => true,
            'array_indentation' => true,
            'blank_line_before_statement' => true,
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'compact_nullable_typehint' => true,
            'escape_implicit_backslashes' => true,
            'explicit_indirect_variable' => true,
            'explicit_string_variable' => true,
            'fully_qualified_strict_types' => true,
            'heredoc_to_nowdoc' => true,
            'method_argument_space' => [
                'on_multiline' => 'ensure_fully_multiline',
            ],
            'method_chaining_indentation' => true,
            'multiline_comment_opening_closing' => true,
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'new_line_for_chained_calls',
            ],
            'no_alternative_syntax' => true,
            'no_binary_string' => true,
            'no_extra_blank_lines' => [
                'tokens' => [
                    'break',
                    'continue',
                    'curly_brace_block',
                    'extra',
                    'parenthesis_brace_block',
                    'return',
                    'square_brace_block',
                    'throw',
                    'use',
                ],
            ],
            'no_null_property_initialization' => true,
            'no_short_echo_tag' => true,
            'no_superfluous_elseif' => true,
            'no_unset_cast' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'ordered_class_elements' => true,
            'php_unit_internal_class' => true,
            'php_unit_method_casing' => true,
            'php_unit_ordered_covers' => true,
            'php_unit_test_class_requires_covers' => true,
            'phpdoc_add_missing_param_annotation' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_order' => true,
            'phpdoc_types_order' => true,
            'phpdoc_var_annotation_correct_order' => true,
            'protected_to_private' => true,
            'return_assignment' => true,
            'simple_to_complex_string_variable' => true,
            'single_line_comment_style' => true,
            'single_line_throw' => false,
        ];
    }

    public function getDescription()
    {
        return 'Rule set as used by the PHP-CS-Fixer development team, highly opinionated.';
    }
}
