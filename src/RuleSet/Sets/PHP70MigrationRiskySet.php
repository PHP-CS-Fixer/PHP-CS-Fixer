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
final class PHP70MigrationRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP56Migration:risky' => true,
            'combine_nested_dirname' => true,
            'declare_strict_types' => true,
            'non_printable_character' => true,
            'random_api_migration' => [
                'replacements' => [
                    'mt_rand' => 'random_int',
                    'rand' => 'random_int',
                ],
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Rules to improve code for PHP 7.0 compatibility.';
    }
}
