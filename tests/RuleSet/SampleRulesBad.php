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

namespace PhpCsFixer\Tests\RuleSet;

/**
 * Sample external RuleSet.
 *
 * This class is not extending the required class `\PhpCsFixer\RuleSet\RuleSetDescriptionInterface`,
 * so it will not be a valid class to be registered as a RuleSet.
 */
class SampleRulesBad
{
    public const NAME = '@RulesBad';

    public function __construct()
    {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Description';
    }

    public function getRules(): array
    {
        return [
            'align_multiline_comment' => false,
        ];
    }
}
