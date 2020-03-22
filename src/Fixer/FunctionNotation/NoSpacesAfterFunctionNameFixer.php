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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.',
            [new CodeSample("<?php\nrequire ('sample.php');\necho (test (3));\nexit  (1);\n\$func ();\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FunctionToConstantFixer.
     * Must run after PowToExponentiationFixer.
     */
    public function getPriority()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array_merge($this->getFunctionyTokenKinds(), [T_STRING]));
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $functionyTokenKinds = $this->getFunctionyTokenKinds();
        $specialConstructTokenKinds = $this->getSpecialConstructTokenKinds();
        $braceTokens = $this->getBraceAfterVariableTokens();

        foreach ($tokens as $index => $token) {
            // looking for start parenthesis
            if (!$token->equals('(')) {
                continue;
            }

            // previous non-whitespace token, can never be `null` always at least PHP open tag before it
            $prevNonWhitespace = $tokens->getPrevNonWhitespace($index);

            // check for special construct with ternary operator
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $nextMeaningful = $tokens->getNextMeaningfulToken($endParenthesisIndex);
            if (
                null !== $nextMeaningful
                && $tokens[$nextMeaningful]->equals('?')
                && $tokens[$prevNonWhitespace]->isGivenKind($specialConstructTokenKinds)
            ) {
                continue;
            }

            // check if it is a function call
            if ($tokens[$prevNonWhitespace]->isGivenKind($functionyTokenKinds)) {
                $this->fixFunctionCall($tokens, $index);
            } elseif ($tokens[$prevNonWhitespace]->isGivenKind(T_STRING)) { // for real function calls or definitions
                $possibleDefinitionIndex = $tokens->getPrevMeaningfulToken($prevNonWhitespace);
                if (!$tokens[$possibleDefinitionIndex]->isGivenKind(T_FUNCTION)) {
                    $this->fixFunctionCall($tokens, $index);
                }
            } elseif ($tokens[$prevNonWhitespace]->equalsAny($braceTokens)) {
                $block = Tokens::detectBlockType($tokens[$prevNonWhitespace]);
                if (
                    Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_PARENTHESIS_BRACE === $block['type']
                ) {
                    $this->fixFunctionCall($tokens, $index);
                }
            }
        }
    }

    /**
     * Fixes whitespaces before parentheses of a function(y) call.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     */
    private function fixFunctionCall(Tokens $tokens, $index)
    {
        // remove space before opening parenthesis
        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens->clearAt($index - 1);
        }
    }

    /**
     * @return array<array|string>
     */
    private function getBraceAfterVariableTokens()
    {
        static $tokens = [
            ')',
            ']',
            [CT::T_DYNAMIC_VAR_BRACE_CLOSE],
            [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE],
        ];

        return $tokens;
    }

    /**
     * Gets the token kinds which can work as function calls.
     *
     * @return int[]
     */
    private function getFunctionyTokenKinds()
    {
        static $tokenKinds = [
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
            T_VARIABLE,
        ];

        return $tokenKinds;
    }

    /**
     * Gets the functiony token kinds which work without parentheses around their argument(s).
     *
     * @return int[]
     */
    private function getSpecialConstructTokenKinds()
    {
        static $tokenKinds = [
            T_ECHO,
            T_PRINT,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
        ];

        return $tokenKinds;
    }
}
