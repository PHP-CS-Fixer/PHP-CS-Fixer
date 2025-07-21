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

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\RuleSet\AbstractMigrationSetDescription;

/**
 * @internal
 */
final class PHPUnit5x5MigrationRiskySet extends AbstractMigrationSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHPUnit5x4Migration:risky' => true,
            'php_unit_mock' => [
                'target' => PhpUnitTargetVersion::VERSION_5_5,
            ],
        ];
    }
}
