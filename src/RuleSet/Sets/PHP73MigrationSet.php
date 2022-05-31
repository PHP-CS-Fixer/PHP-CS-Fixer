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

use PhpCsFixer\RuleSet\AbstractMigrationSetDescription;

/**
 * @internal
 */
final class PHP73MigrationSet extends AbstractMigrationSetDescription
{
    public function getRules(): array
    {
        return [
            '@PHP71Migration' => true,
            'heredoc_indentation' => true,
            'method_argument_space' => ['after_heredoc' => true],
            'trailing_comma_in_multiline' => ['after_heredoc' => true],
            'whitespace_before_statement_end' => [
                'comma_strategy' => 'no_whitespace',
                'semicolon_strategy' => 'no_whitespace',
            ],
        ];
    }
}
