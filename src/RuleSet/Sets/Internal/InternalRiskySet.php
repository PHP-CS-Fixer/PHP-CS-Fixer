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

namespace PhpCsFixer\RuleSet\Sets\Internal;

use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class InternalRiskySet implements RuleSetDefinitionInterface
{
    public function getDescription(): string
    {
        return 'Internal rule set applicable only for PHP CS Fixer repository.';
    }

    public function getName(): string
    {
        return '@self/internal';
    }

    /**
     * Get all rules from rules set.
     *
     * @return array<string, array<string, mixed>|bool>
     */
    public function getRules(): array
    {
        return [
            'PhpCsFixerInternal/configurable_fixer_template' => true,
        ];
    }

    public function isRisky(): bool
    {
        return true;
    }
}
