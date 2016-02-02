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
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ConcatWithoutSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('.');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('.')) {
                continue;
            }

            if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                $tokens->removeLeadingWhitespace($index, " \t");
            }

            if (!$tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                $tokens->removeTrailingWhitespace($index, " \t");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Concatenation should be used without spaces.';
    }
}
