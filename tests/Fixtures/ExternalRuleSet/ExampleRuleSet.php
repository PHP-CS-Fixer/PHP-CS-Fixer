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

namespace PhpCsFixer\Tests\Fixtures\ExternalRuleSet;

use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;

/**
 * Sample external RuleSet.
 *
 * The name is intentionally NOT ending with "Set" to better test real-life usage.
 */
class ExampleRuleSet implements RuleSetDefinitionInterface
{
    public function getName(): string
    {
        return '@Vendor/RuleSet';
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Purpose of example rule set description.';
    }

    public function getRules(): array
    {
        return [
            'align_multiline_comment' => false,
        ];
    }
}
