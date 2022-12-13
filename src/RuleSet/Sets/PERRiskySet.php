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
 * Last updated to PER Coding Style
 */
final class PERRiskySet extends AbstractRuleSetDescription
{
    public function getName(): string
    {
        return 'PER-CS:risky';
    }

    public function getRules(): array
    {
        return [
            '@PSR12:risky' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Alias for the PER-CS rules. It is recommended you use @PER-CS instead.';
    }
}
