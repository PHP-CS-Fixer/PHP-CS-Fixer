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
 * PER Coding Style v3.0.
 *
 * @see https://github.com/php-fig/per-coding-style/blob/3.0.0/spec.md
 */
final class PERCS3x0RiskySet extends AbstractRuleSetDescription
{
    public function getName(): string
    {
        return '@PER-CS3.0:risky';
    }

    public function getRules(): array
    {
        return [
            '@PER-CS2.0:risky' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PER Coding Style 3.0 <https://www.php-fig.org/per/coding-style/>`_.';
    }
}
