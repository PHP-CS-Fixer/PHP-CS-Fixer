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
final class PSR2Set extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PSR1' => true,
            'blank_line_after_namespace' => true,
            'class_definition' => true,
            'constant_case' => true,
            'control_structure_braces' => true,
            'control_structure_continuation_position' => true,
            'curly_braces_position' => true,
            'elseif' => true,
            'function_declaration' => true,
            'indentation_type' => true,
            'line_ending' => true,
            'lowercase_keywords' => true,
            'method_argument_space' => [
                'on_multiline' => 'ensure_fully_multiline',
            ],
            'no_break_comment' => true,
            'no_closing_tag' => true,
            'no_multiple_statements_per_line' => true,
            'no_space_around_double_colon' => true,
            'no_spaces_after_function_name' => true,
            'no_trailing_whitespace' => true,
            'no_trailing_whitespace_in_comment' => true,
            'single_blank_line_at_eof' => true,
            'single_class_element_per_statement' => [
                'elements' => [
                    'property',
                ],
            ],
            'single_import_per_statement' => true,
            'single_line_after_imports' => true,
            'spaces_inside_parentheses' => true,
            'statement_indentation' => true,
            'switch_case_semicolon_to_colon' => true,
            'switch_case_space' => true,
            'visibility_required' => ['elements' => ['method', 'property']],
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PSR-2 <https://www.php-fig.org/psr/psr-2/>`_ standard.';
    }
}
