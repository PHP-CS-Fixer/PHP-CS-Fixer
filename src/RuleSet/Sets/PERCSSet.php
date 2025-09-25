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

use PhpCsFixer\RuleSet\AbstractRuleSetDefinition;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PERCSSet extends AbstractRuleSetDefinition
{
    public function getName(): string
    {
        return '@PER-CS';
    }

    public function getRules(): array
    {
        return [
            '@PER-CS3x0' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow PER Coding Style (https://www.php-fig.org/per/coding-style/), Set is an alias for the latest revision of PER-CS rules - use it if you always want to be in sync with newest PER-CS standard.';
    }
}
