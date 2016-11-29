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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶5.1.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ElseifFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(array(T_IF, T_ELSE));
    }

    /**
     * Replace all `else if` (T_ELSE T_IF) with `elseif` (T_ELSEIF).
     *
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ELSE)) {
                continue;
            }

            $ifTokenIndex = $tokens->getNextMeaningfulToken($index);
            $beforeIfTokenIndex = $tokens->getPrevNonWhitespace($ifTokenIndex);

            // if next meaning token is not T_IF - continue searching, this is not the case for fixing
            if (!$tokens[$ifTokenIndex]->isGivenKind(T_IF)) {
                continue;
            }

            // now we have T_ELSE following by T_IF so we could fix this
            // 1. clear whitespaces between T_ELSE and T_IF
            $tokens[$index + 1]->clear();

            // 2. change token from T_ELSE into T_ELSEIF
            $tokens->overrideAt($index, array(T_ELSEIF, 'elseif'));

            // 3. clear succeeding T_IF
            $tokens[$ifTokenIndex]->clear();

            // 4. clear extra whitespace after T_IF in T_COMMENT,T_WHITESPACE?,T_IF,T_WHITESPACE sequence
            if ($tokens[$beforeIfTokenIndex]->isComment() && $tokens[$ifTokenIndex + 1]->isWhitespace()) {
                $tokens[$ifTokenIndex + 1]->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'The keyword elseif should be used instead of else if so that all control keywords look like single words.';
    }
}
