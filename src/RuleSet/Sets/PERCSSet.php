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
 *
 * @see https://www.php-fig.org/per/coding-style/
 *
 * This Ruleset always points to the latest version of PER-CS.
 * To fix on a specific version of PER-CS, use `PERCS10` (the 1.0 release),
 * `PERCS11` (the 1.1 release), etc.
 */
final class PERCSSet extends AbstractRuleSetDescription
{
    public function getName(): string
    {
        return '@PER-CS';
    }

    public function getRules(): array
    {
        return [
            '@PER-CS1.0' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow the latest `PER Coding Style <https://www.php-fig.org/per/coding-style/>`_.';
    }
}
