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
final class PHP73MigrationSet extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return [
            '@PHP71Migration' => true,
            'heredoc_indentation' => true,
            'method_argument_space' => ['after_heredoc' => true],
            'no_whitespace_before_comma_in_array' => ['after_heredoc' => true],
            'trailing_comma_in_multiline' => ['after_heredoc' => true],
        ];
    }

    public function getDescription()
    {
        return 'Rules to improve code for PHP 7.3 compatibility.';
    }
}
