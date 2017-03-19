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

namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class PowToExponentiationFixer extends AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        // minimal candidate to fix is seven tokens: pow(x,x);
        return PHP_VERSION_ID >= 50600 && $tokens->count() > 7 && $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $candidates = $this->findPowCalls($tokens);

        $numberOfTokensAdded = 0;
        $previousCloseParenthesisIndex = count($tokens);
        foreach (array_reverse($candidates) as $candidate) {
            // if in the previous iteration(s) tokens were added to the collection and this is done within the tokens
            // indexes of the current candidate than the index of the close ')' of the candidate has moved and so
            // the index needs to be updated
            if ($previousCloseParenthesisIndex < $candidate[2]) {
                $previousCloseParenthesisIndex = $candidate[2];
                $candidate[2] += $numberOfTokensAdded;
            } else {
                $previousCloseParenthesisIndex = $candidate[2];
                $numberOfTokensAdded = 0;
            }

            $arguments = $this->getArguments($tokens, $candidate[1], $candidate[2]);
            if (2 !== count($arguments)) {
                continue;
            }

            $numberOfTokensAdded += $this->fixPowToExponentiation(
                $tokens,
                $candidate[0], // functionNameIndex,
                $candidate[1], // openParenthesisIndex,
                $candidate[2], // closeParenthesisIndex,
                $arguments
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
           'Converts `pow()` to the `**` operator. Requires PHP >= 5.6.',
            array(
                new VersionSpecificCodeSample(
                    "<?php\n pow(\$a, 1);",
                    new VersionSpecification(50600)
                ),
            ),
            null,
            'Risky when the function `pow()` is overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before BinaryOperatorSpacesFixer
        return 1;
    }

    /**
     * @param Tokens $tokens
     *
     * @return array[]
     */
    private function findPowCalls(Tokens $tokens)
    {
        $candidates = array();

        // Minimal candidate to fix is seven tokens: pow(x,x);
        $end = count($tokens) - 6;

        // First possible location is after the open token: 1
        for ($i = 1; $i < $end; ++$i) {
            $candidate = $this->find('pow', $tokens, $i, $end);
            if (null === $candidate) {
                break;
            }

            $i = $candidate[1]; // proceed to openParenthesisIndex
            $candidates[] = $candidate;
        }

        return $candidates;
    }

    /**
     * @param Tokens         $tokens
     * @param int            $functionNameIndex
     * @param int            $openParenthesisIndex
     * @param int            $closeParenthesisIndex
     * @param array<int,int> $arguments
     *
     * @return int number of tokens added to the collection
     */
    private function fixPowToExponentiation(Tokens $tokens, $functionNameIndex, $openParenthesisIndex, $closeParenthesisIndex, array $arguments)
    {
        // find the argument separator ',' directly after the last token of the first argument;
        // replace it with T_POW '**'
        $tokens->overrideAt(
            $tokens->getNextTokenOfKind(reset($arguments), array(',')),
            new Token(array(T_POW, '**'))
        );

        // clean up the function call tokens prt. I
        $tokens[$closeParenthesisIndex]->clear();

        $added = 0;
        // check if the arguments need to be wrapped in parenthesis
        foreach (array_reverse($arguments, true) as $argumentStartIndex => $argumentEndIndex) {
            if ($this->isParenthesisNeeded($tokens, $argumentStartIndex, $argumentEndIndex)) {
                $tokens->insertAt($argumentEndIndex + 1, new Token(')'));
                $tokens->insertAt($argumentStartIndex, new Token('('));
                $added += 2;
            }
        }

        // clean up the function call tokens prt. II
        $tokens[$openParenthesisIndex]->clear();
        $tokens[$functionNameIndex]->clear();

        $prev = $tokens->getPrevMeaningfulToken($functionNameIndex);
        if ($tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
            $tokens[$prev]->clear();
        }

        return $added;
    }

    /**
     * @param Tokens $tokens
     * @param int    $argumentStartIndex
     * @param int    $argumentEndIndex
     *
     * @return bool
     */
    private function isParenthesisNeeded(Tokens $tokens, $argumentStartIndex, $argumentEndIndex)
    {
        static $allowedKinds = array(
            T_DNUMBER, T_LNUMBER, T_VARIABLE, T_STRING, T_OBJECT_OPERATOR, T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_CAST,
            T_INT_CAST, T_INC, T_DEC, T_NS_SEPARATOR, T_WHITESPACE, T_DOUBLE_COLON, T_LINE, T_COMMENT, T_DOC_COMMENT,
            CT::T_NAMESPACE_OPERATOR,
        );

        for ($i = $argumentStartIndex; $i <= $argumentEndIndex; ++$i) {
            if ($tokens[$i]->isGivenKind($allowedKinds) || $tokens[$i]->isEmpty()) {
                continue;
            }

            if (null !== $blockType = Tokens::detectBlockType($tokens[$i])) {
                $i = $tokens->findBlockEnd($blockType['type'], $i);

                continue;
            }

            if ($tokens[$i]->equals('$')) {
                $i = $tokens->getNextMeaningfulToken($i);
                if ($tokens[$i]->isGivenKind(CT::T_DYNAMIC_VAR_BRACE_OPEN)) {
                    $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE, $i);

                    continue;
                }
            }

            if ($tokens[$i]->equals('+') && $tokens->getPrevMeaningfulToken($i) < $argumentStartIndex) {
                continue;
            }

            return true;
        }

        return false;
    }
}
