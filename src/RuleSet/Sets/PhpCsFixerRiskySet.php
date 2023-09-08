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
final class PhpCsFixerRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PER-CS2.0:risky' => true,
            '@Symfony:risky' => true,
            'comment_to_phpdoc' => true,
            'final_internal_class' => true,
            'get_class_to_class_keyword' => false,
            'modernize_strpos' => false,
            // @TODO: consider switching to `true`, like in @Symfony
            'native_constant_invocation' => [
                'fix_built_in' => false,
                'include' => [
                    'DIRECTORY_SEPARATOR',
                    'PHP_INT_SIZE',
                    'PHP_SAPI',
                    'PHP_VERSION_ID',
                ],
                'scope' => 'namespaced',
                'strict' => true,
            ],
            'no_alias_functions' => [
                'sets' => [
                    '@all',
                ],
            ],
            'no_unreachable_default_argument_value' => true,
            'no_unset_on_property' => true,
            'php_unit_data_provider_name' => true,
            'php_unit_data_provider_return_type' => true,
            'php_unit_strict' => true,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'static_lambda' => true,
            'strict_comparison' => true,
            'strict_param' => true,
            'yield_from_array_to_yields' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rule set as used by the PHP-CS-Fixer development team, highly opinionated.';
    }
}
