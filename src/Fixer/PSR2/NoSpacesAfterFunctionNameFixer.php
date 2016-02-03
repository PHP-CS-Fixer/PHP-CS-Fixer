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
 * Fixer for rules defined in PSR2 ¶4.6.
 *
 * @author Varga Bence <vbence@czentral.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoSpacesAfterFunctionNameFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array_merge($this->getFunctionyTokenKinds(), array(T_STRING)));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $functionyTokens = $this->getFunctionyTokenKinds();
        $languageConstructionTokens = $this->getLanguageConstructionTokenKinds();

        foreach ($tokens as $index => $token) {
            // looking for start brace
            if (!$token->equals('(')) {
                continue;
            }

            // last non-whitespace token
            $lastTokenIndex = $tokens->getPrevNonWhitespace($index);

            if (null === $lastTokenIndex) {
                continue;
            }

            // check for ternary operator
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $nextNonWhiteSpace = $tokens->getNextMeaningfulToken($endParenthesisIndex);
            if (
                null !== $nextNonWhiteSpace
                && $tokens[$nextNonWhiteSpace]->equals('?')
                && $tokens[$lastTokenIndex]->isGivenKind($languageConstructionTokens)
            ) {
                continue;
            }

            // check if it is a function call
            if ($tokens[$lastTokenIndex]->isGivenKind($functionyTokens)) {
                $this->fixFunctionCall($tokens, $index);
            } elseif ($tokens[$lastTokenIndex]->isGivenKind(T_STRING)) { // for real function calls or definitions
                $possibleDefinitionIndex = $tokens->getPrevMeaningfulToken($lastTokenIndex);
                if (!$tokens[$possibleDefinitionIndex]->isGivenKind(T_FUNCTION)) {
                    $this->fixFunctionCall($tokens, $index);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.';
    }

    /**
     * Fixes whitespaces around braces of a function(y) call.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     */
    private function fixFunctionCall(Tokens $tokens, $index)
    {
        // remove space before opening brace
        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens[$index - 1]->clear();
        }
    }

    /**
     * Gets the token kinds which can work as function calls.
     *
     * @return int[] Token names.
     */
    private function getFunctionyTokenKinds()
    {
        static $tokens = null;

        if (null === $tokens) {
            $tokens = array(
                T_ARRAY,
                T_ECHO,
                T_EMPTY,
                T_EVAL,
                T_EXIT,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_ISSET,
                T_LIST,
                T_PRINT,
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_UNSET,
            );
        }

        return $tokens;
    }

    /**
     * Gets the token kinds of actually language construction.
     *
     * @return int[]
     */
    private function getLanguageConstructionTokenKinds()
    {
        static $languageConstructionTokens = array(
            T_ECHO,
            T_PRINT,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
        );

        return $languageConstructionTokens;
    }
}
