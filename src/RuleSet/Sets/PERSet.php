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

/**
 * @internal
 *
 * Last updated to PER Coding Style v1.1.0.
 */
final class PERSet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return [
            '@PSR12' => true,
            'heredoc_indentation' => true,
            'heredoc_to_nowdoc' => true,
            'method_chaining_indentation' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_trailing_comma_in_singleline' => true,
            'trailing_comma_in_multiline' => [
                'after_heredoc' => true,
                'elements' => ['arguments', 'arrays', 'match', 'parameters'],
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Rules that follow `PER Coding Style <https://www.php-fig.org/per/coding-style/>`_.';
    }
}
