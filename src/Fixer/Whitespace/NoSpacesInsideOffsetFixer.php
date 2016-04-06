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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesInsideOffsetFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('[');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('[')) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);

            // remove space after opening `[`
            $this->removeWhitespaceToken($tokens[$index + 1]);

            // remove space before closing `]`
            $this->removeWhitespaceToken($tokens[$endIndex - 1]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be spaces between the offset square braces and its contained values.';
    }

    /**
     * Removes the token if it is single line whitespace.
     *
     * @param Token $token
     */
    private function removeWhitespaceToken(Token $token)
    {
        if ($token->isWhitespace(" \t")) {
            $token->clear();
        }
    }
}
