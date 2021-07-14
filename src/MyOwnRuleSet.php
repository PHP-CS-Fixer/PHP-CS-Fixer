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

namespace PhpCsFixer;

use PhpCsFixer\RuleSet\AbstractRuleSetDescription;

/**
 * @internal
 */
final class MyOwnRuleSet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            'array_syntax' => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PSR-1 <https://www.php-fig.org/psr/psr-1/>`_ standard.';
    }
}
