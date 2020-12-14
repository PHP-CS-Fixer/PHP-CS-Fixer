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

namespace PhpCsFixer\RuleSet\Sets;

use PhpCsFixer\RuleSet\AbstractRuleSetDescription;

/**
 * @internal
 */
final class PSR1Set extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return [
            'encoding' => true,
            'full_opening_tag' => true,
        ];
    }

    public function getDescription()
    {
        return 'Rules that follow `PSR-1 <https://www.php-fig.org/psr/psr-1/>`_ standard.';
    }
}
