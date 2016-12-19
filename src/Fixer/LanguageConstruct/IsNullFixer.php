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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

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

            // 0 and 1 accordingly are "is_null", "(" tokens
            $matches = array_keys($matches);

            // move the cursor just after the sequence
            list ($isNullIndex, $currIndex) = $matches;

            // skip all expressions which are not a function reference
            $inversionCandidateIndex = $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            // handle function references with namespaces
            if ($prevToken->isGivenKind(T_NS_SEPARATOR)) {
                $inversionCandidateIndex = $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                /* @var Token $twicePrevToken */
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION, T_STRING, CT::T_NAMESPACE_OPERATOR))) {
                    continue;
                }

                // get rid of the root namespace when it used and check if the inversion operator provided
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
                new Token(array(T_WHITESPACE, ' ')),
            );

            /* before getting rind of `()` around a parameter, ensure it's not a ternary of any kind */
            $referenceEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $matches[1]);
            $ternaryTokens = $tokens->findGivenKind(array('?', '?:', '??'), $matches[1], $referenceEnd);
            $isContainTernary = count($ternaryTokens) > 0;

            if (!$isContainTernary) {
                // closing parenthesis removed with leading spaces
                $tokens->removeLeadingWhitespace($referenceEnd);
                $tokens[$referenceEnd]->clear();

                // opening parenthesis removed with trailing spaces
                $tokens->removeLeadingWhitespace($matches[1]);
                $tokens->removeTrailingWhitespace($matches[1]);
                $tokens[$matches[1]]->clear();
            }

            $tokens->overrideRange($isNullIndex, $isNullIndex, $replacement);

            // nested is_null's support
            $currIndex = $isNullIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replaces is_null(parameter) expression with `null === parameter`.',
            array(new CodeSample("<?php\n\$a = is_null(\$b);")),
            null,
            null,
            null,
            'Risky when the function `is_null()` is overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }
}
