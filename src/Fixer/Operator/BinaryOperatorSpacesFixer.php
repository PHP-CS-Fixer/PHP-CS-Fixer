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

use PhpCsFixer\AbstractAlignFixerHelper;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
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
    public function getDefinition()
    {
        return 'Ensure that there is atleast one space on either side of each binary operator (= and =>).';
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixWhiteSpaceAroundOperator(Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind(T_DOUBLE_ARROW) || $tokens[$index]->equals('=')) {
            // fix white space after operator
            if (!$tokens[$index + 1]->isWhitespace()) {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            // fix white space before operator
            if (!$tokens[$index - 1]->isWhitespace()) {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return false|int
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
