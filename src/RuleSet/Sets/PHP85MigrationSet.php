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
final class PHP85MigrationSet extends AbstractMigrationSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP83Migration' => true,
            'new_expression_parentheses' => true,
            'nullable_type_declaration_for_default_null_value' => true,
        ];
    }
}
