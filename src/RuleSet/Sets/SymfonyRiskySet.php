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
final class SymfonyRiskySet extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return [
            'dir_constant' => true,
            'ereg_to_preg' => true,
            'error_suppression' => true,
            'fopen_flag_order' => true,
            'fopen_flags' => [
                'b_mode' => false,
            ],
            'function_to_constant' => [
                'functions' => [
                    'get_called_class',
                    'get_class',
                    'get_class_this',
                    'php_sapi_name',
                    'phpversion',
                    'pi',
                ],
            ],
            'implode_call' => true,
            'is_null' => true,
            'modernize_types_casting' => true,
            'native_constant_invocation' => [
                'fix_built_in' => false,
                'include' => [
                    'DIRECTORY_SEPARATOR',
                    'PHP_SAPI',
                    'PHP_VERSION_ID',
                ],
                'scope' => 'namespaced',
            ],
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
            'non_printable_character' => true,
            'php_unit_construct' => true,
            'php_unit_mock_short_will_return' => true,
            'psr4' => true,
            'self_accessor' => true,
            'set_type_to_cast' => true,
        ];
    }

    public function getDescription()
    {
        return 'Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.';
    }
}
