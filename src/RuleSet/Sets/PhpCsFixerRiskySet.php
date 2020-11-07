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
final class PhpCsFixerRiskySet extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return [
            '@Symfony:risky' => true,
            'comment_to_phpdoc' => true,
            'final_internal_class' => true,
            'no_alias_functions' => [
                'sets' => [
                    '@all',
                ],
            ],
            'no_unreachable_default_argument_value' => true,
            'no_unset_on_property' => true,
            'php_unit_strict' => true,
            'php_unit_test_case_static_method_calls' => true,
            'strict_comparison' => true,
            'strict_param' => true,
        ];
    }

    public function getDescription()
    {
        return 'Rule set as used by the PHP-CS-Fixer development team, highly opinionated.';
    }
}
