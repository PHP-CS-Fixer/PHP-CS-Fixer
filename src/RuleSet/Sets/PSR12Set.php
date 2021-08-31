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
final class PSR12Set extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PSR2' => true,
            'blank_line_after_opening_tag' => true,
            'braces' => [
                'allow_single_line_anonymous_class_with_empty_body' => true,
            ],
            'class_definition' => ['space_before_parenthesis' => true], // defined in PSR12 ¶8. Anonymous Classes
            'compact_nullable_typehint' => true,
            'declare_equal_normalize' => true,
            'lowercase_cast' => true,
            'lowercase_static_reference' => true,
            'new_with_braces' => true,
            'no_blank_lines_after_class_opening' => true,
            'no_leading_import_slash' => true,
            'no_whitespace_in_blank_line' => true,
            'ordered_class_elements' => [
                'order' => [
                    'use_trait',
                ],
            ],
            'ordered_imports' => [
                'imports_order' => [
                    'class',
                    'function',
                    'const',
                ],
                'sort_algorithm' => 'none',
            ],
            'return_type_declaration' => true,
            'short_scalar_cast' => true,
            'single_blank_line_before_namespace' => true,
            'single_trait_insert_per_statement' => true,
            'ternary_operator_spaces' => true,
            'visibility_required' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PSR-12 <https://www.php-fig.org/psr/psr-12/>`_ standard.';
    }
}
