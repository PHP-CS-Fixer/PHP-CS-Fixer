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

namespace PhpCsFixer\Fixer\PSR2;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.6, ¶5.
 *
 * @author Marc Aubé
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoSpacesInsideParenthesisFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('(')) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            // ignore parenthesis for T_ARRAY
            if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_ARRAY)) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            // remove space after opening `(`
            $this->removeSpaceAroundToken($tokens, $index, 1);

            // remove space after closing `)` if it is not `list($a, $b, )` case
            if (!$tokens[$tokens->getPrevMeaningfulToken($endIndex)]->equals(',')) {
                $this->removeSpaceAroundToken($tokens, $endIndex, -1);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.';
    }

    /**
     * Remove spaces on one side of the token at a given index.
     *
     * @param Tokens $tokens A collection of code tokens
     * @param int    $index  The token index
     * @param int    $offset The offset where to start looking for spaces
     */
    private function removeSpaceAroundToken(Tokens $tokens, $index, $offset)
    {
        if (!isset($tokens[$index + $offset])) {
            return;
        }

        $token = $tokens[$index + $offset];

        if ($token->isWhitespace() && false === strpos($token->getContent(), "\n")) {
            if (isset($tokens[$index + $offset - 1])) {
                $prevToken = $tokens[$index + $offset - 1];
                if ($prevToken->isComment() && false !== strpos($prevToken->getContent(), "\n")) {
                    return;
                }
            }

            $token->clear();
        }
    }
}
