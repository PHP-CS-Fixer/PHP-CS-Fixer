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

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NotOperatorWithSuccessorSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($tokensAnalyzer->isUnaryPredecessorOperator($index) && $token->equals('!')) {
                if (!$tokens[$index + 1]->isWhitespace()) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                } else {
                    $tokens[$index + 1]->setContent(' ');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Logical NOT operators (!) should have one trailing whitespace.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the UnaryOperatorSpacesFixer
        return -10;
    }
}
