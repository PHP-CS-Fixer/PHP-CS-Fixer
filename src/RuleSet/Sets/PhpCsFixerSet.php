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
final class PhpCsFixerSet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PER-CS' => true,
            '@Symfony' => true,
            'blank_line_before_statement' => [
                'statements' => [
                    'break',
                    'case',
                    'continue',
                    'declare',
                    'default',
                    'exit',
                    'goto',
                    'include',
                    'include_once',
                    'phpdoc',
                    'require',
                    'require_once',
                    'return',
                    'switch',
                    'throw',
                    'try',
                    'yield',
                    'yield_from',
                ],
            ],
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'empty_loop_body' => true,
            'explicit_indirect_variable' => true,
            'explicit_string_variable' => true,
            'fully_qualified_strict_types' => [
                'import_symbols' => true,
            ],
            'heredoc_to_nowdoc' => true,
            'method_argument_space' => [
                'on_multiline' => 'ensure_fully_multiline',
            ],
            'method_chaining_indentation' => true,
            'multiline_comment_opening_closing' => true,
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'new_line_for_chained_calls',
            ],
            'no_extra_blank_lines' => [
                'tokens' => [
                    'attribute',
                    'break',
                    'case',
                    'continue',
                    'curly_brace_block',
                    'default',
                    'extra',
                    'parenthesis_brace_block',
                    'return',
                    'square_brace_block',
                    'switch',
                    'throw',
                    'use',
                ],
            ],
            'no_superfluous_elseif' => true,
            'no_superfluous_phpdoc_tags' => [
                'allow_mixed' => true,
                'remove_inheritdoc' => true,
            ],
            'no_unneeded_control_parentheses' => [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'negative_instanceof',
                    'others',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                ],
            ],
            'no_useless_else' => true,
            'no_useless_return' => true,
            'no_whitespace_before_comma_in_array' => ['after_heredoc' => true],
            'ordered_class_elements' => true,
            'ordered_types' => true,
            'php_unit_internal_class' => true,
            'php_unit_test_class_requires_covers' => true,
            'phpdoc_add_missing_param_annotation' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_order_by_value' => true,
            'phpdoc_types_order' => true,
            'phpdoc_var_annotation_correct_order' => true,
            'protected_to_private' => true,
            'return_assignment' => true,
            'self_static_accessor' => true,
            'single_line_comment_style' => true,
            'single_line_empty_body' => true,
            'single_line_throw' => false,
            'string_implicit_backslashes' => true,
            'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        ];
    }

    public function getDescription(): string
    {
        return 'Rule set as used by the PHP-CS-Fixer development team, highly opinionated.';
    }
}
