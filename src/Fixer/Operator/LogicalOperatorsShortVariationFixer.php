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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class LogicalOperatorsShortVariationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Operators `&&` and `||` should be should be used instead of `and` and `or`, respectively.',
            [
                new CodeSample("<?php\n\$solution = \$p and \$q;\n"),
                new CodeSample("<?php\n\$solution = \$p or \$q;\n"),
            ],
            null,
            'replaced pair operate at different precedences'
        );
    }

    public function isRisky()
    {
        return true;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_LOGICAL_AND, T_LOGICAL_OR]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_LOGICAL_AND)) {
                $tokens[$index] = new Token([T_BOOLEAN_AND, '&&']);

                continue;
            }

            if ($token->isGivenKind(T_LOGICAL_OR)) {
                $tokens[$index] = new Token([T_BOOLEAN_OR, '||']);

                continue;
            }
        }
    }
}
