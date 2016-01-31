<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Contrib;

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
            $token = $tokens[$index];

            if ($token->equals('.')) {
                if (!$tokens[$index + 1]->isWhitespace()) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                }

                if (!$tokens[$index - 1]->isWhitespace()) {
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
                }
            }
        }
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
