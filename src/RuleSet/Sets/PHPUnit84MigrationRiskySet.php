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
use PhpCsFixer\RuleSet\AbstractRuleSetDescription;

/**
 * @internal
 */
final class PHPUnit84MigrationRiskySet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHPUnit60Migration:risky' => true,
            '@PHPUnit75Migration:risky' => true,
            'php_unit_expectation' => [
                'target' => PhpUnitTargetVersion::VERSION_8_4,
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Rules to improve tests code for PHPUnit 8.4 compatibility.';
    }
}
