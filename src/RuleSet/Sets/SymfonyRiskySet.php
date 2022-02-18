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
final class SymfonyRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP56Migration:risky' => true,
            '@PSR12:risky' => true,
            'array_push' => true,
            'combine_nested_dirname' => true,
            'dir_constant' => true,
            'ereg_to_preg' => true,
            'error_suppression' => true,
            'fopen_flag_order' => true,
            'fopen_flags' => [
                'b_mode' => false,
            ],
            'function_to_constant' => true,
            'implode_call' => true,
            'is_null' => true,
            'logical_operators' => true,
            'modernize_types_casting' => true,
            'native_constant_invocation' => true,
            'native_function_invocation' => [
                'include' => [
                    '@compiler_optimized',
                ],
                'scope' => 'namespaced',
                'strict' => true,
            ],
            'no_alias_functions' => true,
            'no_homoglyph_names' => true,
            'no_php4_constructor' => true,
            'no_unneeded_final_method' => true,
            'no_unreachable_default_argument_value' => false,
            'no_useless_sprintf' => true,
            'non_printable_character' => true,
            'ordered_traits' => true,
            'php_unit_construct' => true,
            'php_unit_mock_short_will_return' => true,
            'php_unit_set_up_tear_down_visibility' => true,
            'php_unit_test_annotation' => true,
            'psr_autoloading' => true,
            'self_accessor' => true,
            'set_type_to_cast' => true,
            'string_length_to_empty' => true,
            'string_line_ending' => true,
            'ternary_to_elvis_operator' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.';
    }
}
