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
use PhpCsFixer\RuleSet\DeprecatedRuleSetDescriptionInterface;

/**
 * @internal
 *
 * PER Coding Style v1.0.
 *
 * @TODO 4.0 Remove this class
 *
 * @deprecated Use `@PER-CS2.0` instead.
 * @see https://github.com/php-fig/per-coding-style/blob/1.0.0/spec.md
 */
final class PERCS1x0Set extends AbstractRuleSetDescription implements DeprecatedRuleSetDescriptionInterface
{
    public function getName(): string
    {
        return '@PER-CS1.0';
    }

    public function getRules(): array
    {
        return [
            '@PSR12' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PER Coding Style 1.0 <https://www.php-fig.org/per/coding-style/>`_.';
    }

    public function getSuccessorsNames(): array
    {
        return ['@PER-CS2.0'];
    }
}
