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
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class BinaryOperatorSpacesFixer extends AbstractFixer
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

        // last and first tokens cannot be an operator
        for ($index = $tokens->count() - 2; $index >= 0; --$index) {
            if (!$tokensAnalyzer->isBinaryOperator($index)) {
                continue;
            }

            $isDeclare = $this->isDeclareStatement($tokens, $index);
            if (false !== $isDeclare) {
                $index = $isDeclare; // skip `declare(foo ==bar)`, see `declare_equal_normalize`
            } else {
                $this->fixWhiteSpaceAroundOperator($tokens, $index);
            }

            // previous of binary operator is now never an operator / previous of declare statement cannot be an operator
            --$index;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Binary operators should be surrounded by at least one space.';
    }

    private function fixWhiteSpaceAroundOperator(Tokens $tokens, $index)
    {
        // do not change the alignment of `=>` or `=`, see `(un)align_double_arrow`, `(un)align_equals`
        $preserveAlignment = $tokens[$index]->isGivenKind(T_DOUBLE_ARROW) || $tokens[$index]->equals('=');

        // fix white space after operator
        if ($tokens[$index + 1]->isWhitespace()) {
            $content = $tokens[$index + 1]->getContent();
            if (!$preserveAlignment && ' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($index + 1)]->isComment()) {
                $tokens[$index + 1]->setContent(' ');
            }
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }

        // fix white space before operator
        if ($tokens[$index - 1]->isWhitespace()) {
            $content = $tokens[$index - 1]->getContent();
            if (!$preserveAlignment && ' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                $tokens[$index - 1]->setContent(' ');
            }
        } else {
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool|int
     */
    private function isDeclareStatement(Tokens $tokens, $index)
    {
        $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_STRING)) {
            $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
            if ($tokens[$prevMeaningfulIndex]->equals('(')) {
                $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_DECLARE)) {
                    return $prevMeaningfulIndex;
                }
            }
        }

        return false;
    }
}
