<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class IsNullFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $sequenceNeeded = array(array(T_STRING, 'is_null'), '(');

        $currIndex = 0;
        while (null !== $currIndex) {
            $matches = $tokens->findSequence($sequenceNeeded, $currIndex, $tokens->count() - 1, false);

            // stop looping if didn't find any new matches
            if (null === $matches) {
                break;
            }

            // 0 and 1 accordingly are "is_null", "("
            $matches = array_keys($matches);

            // move cursor just after sequence
            $currIndex = $matches[1];
            $isNullIndex = $matches[0];

            // skip expressions which are not function reference
            $inversionCandidateIndex = $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            // handle function reference with namespaces
            if ($prevToken->isGivenKind(T_NS_SEPARATOR)) {
                $inversionCandidateIndex = $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                /* @var Token $twicePrevToken */
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_NEW, T_STRING, CT_NAMESPACE_OPERATOR))) {
                    continue;
                }

                // get rid of root namespace when it used and check if inversion provided
                $tokens->removeTrailingWhitespace($prevTokenIndex);
                $tokens[$prevTokenIndex]->clear();
            }

            // check if inversion being used, text comparison is due to not existing constant
            $isInvertedNullCheck = false;
            if ('!' === $tokens[$inversionCandidateIndex]->getContent()) {
                $isInvertedNullCheck = true;

                // get rid of inverting for proper transformations
                $tokens->removeTrailingWhitespace($inversionCandidateIndex);
                $tokens[$inversionCandidateIndex]->clear();
            }

            // sequence which we'll use as a replacement
            $replacement = array(
                new Token(array(T_STRING, 'null')),
                new Token(array(T_WHITESPACE, ' ')),
                new Token($isInvertedNullCheck ? array(T_IS_NOT_IDENTICAL, '!==') : array(T_IS_IDENTICAL, '===')),
                $replacement [] = new Token(array(T_WHITESPACE, ' ')),
            );

            // closing parenthesis removed with leading spaces
            $referenceEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $matches[1]);
            $tokens->removeLeadingWhitespace($referenceEnd);
            $tokens[$referenceEnd]->clear();

            // opening parenthesis removed with trailing spaces
            $tokens->removeLeadingWhitespace($matches[1]);
            $tokens->removeTrailingWhitespace($matches[1]);
            $tokens[$matches[1]]->clear();

            $tokens->overrideRange($isNullIndex, $isNullIndex, $replacement);

            // nested is_null's support
            $currIndex = $isNullIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces is_null(parameter) expression with null === parameters.';
    }
}
