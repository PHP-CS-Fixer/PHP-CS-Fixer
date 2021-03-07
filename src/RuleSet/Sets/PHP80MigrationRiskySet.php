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
final class PHP80MigrationRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP74Migration:risky' => true,
            'no_alias_functions' => [
                'sets' => [
                    '@all',
                ],
            ],
            'no_php4_constructor' => true,
            'no_unneeded_final_method' => true, // final private method (not constructor) are no longer allowed >= PHP8.0
            'no_unreachable_default_argument_value' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules to improve code for PHP 8.0 compatibility.';
    }
}
