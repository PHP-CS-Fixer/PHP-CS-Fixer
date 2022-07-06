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
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 *         Matt Allan <https://github.com/matt-allan>
 */
final class LaravelRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            'no_alias_functions' => true,
            'no_unreachable_default_argument_value' => true,
            'psr_autoloading' => true,
            'self_accessor' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Risky rules covering Laravel 8.0 style.';
    }
}
