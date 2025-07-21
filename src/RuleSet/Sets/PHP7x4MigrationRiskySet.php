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

use PhpCsFixer\RuleSet\AbstractMigrationSetDescription;

/**
 * @internal
 */
final class PHP7x4MigrationRiskySet extends AbstractMigrationSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP7x1Migration:risky' => true,
            'implode_call' => true,
            'no_alias_functions' => true,
            'use_arrow_functions' => true,
        ];
    }
}
