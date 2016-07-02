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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ConcatWithSpacesFixer extends AbstractFixer
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
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->equals('.')) {
                continue;
            }

            $this->fixWhiteSpaceAroundConcatToken($tokens, $index, 1);
            $this->fixWhiteSpaceAroundConcatToken($tokens, $index, -1);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  Index of concat token
     * @param int    $offset 1 or -1
     */
    private function fixWhiteSpaceAroundConcatToken(Tokens $tokens, $index, $offset)
    {
        $offsetIndex = $index + $offset;

        if (!$tokens[$offsetIndex]->isWhitespace()) {
            $tokens->insertAt($index + (1 === $offset ?: 0), new Token(array(T_WHITESPACE, ' ')));

            return;
        }

        if (false !== strpos($tokens[$offsetIndex]->getContent(), "\n")) {
            return;
        }

        if ($tokens[$index + $offset * 2]->isComment()) {
            return;
        }

        $tokens[$offsetIndex]->setContent(' ');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Concatenation should be used with at least one whitespace around.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the ConcatWithoutSpacesFixer
        return -10;
    }
}
