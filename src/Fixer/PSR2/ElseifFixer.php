<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR2;

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
        // handle `T_ELSE T_WHITESPACE T_IF` treated as single `T_ELSEIF` by HHVM
        // see https://github.com/facebook/hhvm/issues/4796
        if (defined('HHVM_VERSION') && $tokens->isTokenKindFound(T_ELSEIF)) {
            return true;
        }

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

            $nextIndex = $tokens->getNextNonWhitespace($index);
            $nextToken = $tokens[$nextIndex];

            // if next meaning token is not T_IF - continue searching, this is not the case for fixing
            if (!$nextToken->isGivenKind(T_IF)) {
                continue;
            }

            // now we have T_ELSE following by T_IF so we could fix this
            // 1. clear whitespaces between T_ELSE and T_IF
            $tokens[$index + 1]->clear();

            // 2. change token from T_ELSE into T_ELSEIF
            $tokens->overrideAt($index, array(T_ELSEIF, 'elseif'));

            // 3. clear succeeding T_IF
            $nextToken->clear();
        }

        // handle `T_ELSE T_WHITESPACE T_IF` treated as single `T_ELSEIF` by HHVM
        // see https://github.com/facebook/hhvm/issues/4796
        if (defined('HHVM_VERSION')) {
            foreach ($tokens->findGivenKind(T_ELSEIF) as $token) {
                $token->setContent('elseif');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The keyword elseif should be used instead of else if so that all control keywords look like single words.';
    }
}
