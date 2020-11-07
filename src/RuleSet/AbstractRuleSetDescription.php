<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\RuleSet;

/**
 * @internal
 */
abstract class AbstractRuleSetDescription implements RuleSetDescriptionInterface
{
    public function __construct()
    {
    }

    public function getName()
    {
        $name = substr(static::class, 1 + strrpos(static::class, '\\'), -3);

        return '@'.str_replace('Risky', ':risky', $name);
    }

    public function isRisky()
    {
        return false !== strpos(static::class, 'Risky');
    }
}
