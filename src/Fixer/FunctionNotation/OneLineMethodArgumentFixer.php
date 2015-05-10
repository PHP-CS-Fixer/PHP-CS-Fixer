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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Konrad Cerny <info@konradcerny.cz>
 */
final class OneLineMethodArgumentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        /** @var Token $token */
        foreach ($tokens as $index => $token) {
            if (!$token->equals('(')) {
                continue;
            }

            if (
                // function
                $this->isFunction($tokens, $index - 3)
                // callable
                || $this->isFunction($tokens, $tokens->getPrevMeaningfulToken($index))
                // function call
                || $this->isFunction($tokens, $tokens->getPrevMeaningfulToken($index), T_STRING)
            ) {
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                if ($this->hasNewLineBetweenParenthesis($tokens, $index, $endIndex)) {
                    $this->fixFunctionArguments($tokens, $index, $endIndex);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 1; // this fixer should run before align fixers
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Make sure method/function arguments are on a single line, or on each their own.';
    }

    /**
     * Fix arguments for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     * @param int    $endFunctionIndex   End parenthesis position
     */
    private function fixFunctionArguments(Tokens $tokens, $startFunctionIndex, $endFunctionIndex)
    {
        for ($index = $startFunctionIndex; $index <= $endFunctionIndex; ++$index) {
            $token = $tokens[$index];

            if ($token->equals('(')) {
                if (!$this->isNewLine($tokens[$index + 1]) && $tokens[$index + 1]->getContent() !== ')') {
                    $this->addNewLineOn($tokens, $index, $endFunctionIndex);
                }
            }
            if ($token->equals(')')) {
                if (!$this->isNewLine($tokens[$index - 1]) && $tokens[$index - 1]->getContent() !== '(') {
                    $this->addNewLineOn($tokens, $index - 1, $endFunctionIndex, '');
                }
            }

            if ($token->equals(',')) {
                $this->fixNewLine($tokens, $index, $endFunctionIndex);
            }
        }
    }

    /**
     * Inserts new line after comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $endFunctionIndex
     */
    private function fixNewLine(Tokens $tokens, $index, &$endFunctionIndex)
    {
        if (
            $tokens[$index + 1]->isWhitespace()
            && $tokens[$index + 2]->isComment()
            && $this->isNewLine($tokens[$index + 3])
        ) {
            $index += 3;
        }

        $this->addNewLineOn($tokens, $index, $endFunctionIndex);
    }

    /**
     * Adds new line to the given token.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $endFunctionIndex
     * @param string $indention
     */
    private function addNewLineOn(Tokens $tokens, $index, &$endFunctionIndex, $indention = '    ')
    {
        $newToken = new Token(array(T_WHITESPACE, "\n".$indention));

        if ($tokens[$index]->isWhitespace()) {
            $tokens->overrideAt($index, $newToken);
        } else {
            if ($tokens[$index + 1]->isWhitespace()) {
                $tokens->overrideAt($index + 1, $newToken);
            } else {
                $tokens->insertAt($index + 1, $newToken);
                ++$endFunctionIndex;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return bool
     */
    private function hasNewLineBetweenParenthesis(Tokens $tokens, $startIndex, $endIndex)
    {
        /** @var Token $token */
        foreach ($tokens as $index => $token) {
            if ($index <= $startIndex || $index >= $endIndex) {
                continue;
            }
            if ($this->isNewLine($token)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int|null
     */
    private function getFunctionCloseParenthesisIndex(Tokens $tokens, $index)
    {
        $tokenIndex = $tokens->getNextNonWhitespace($index);

        return $tokens[$tokenIndex]->getContent() === ')' && $tokens[$tokenIndex - 1]->getContent() !== '('
            ? $tokenIndex
            : $this->getFunctionCloseParenthesisIndex($tokens, $tokenIndex);
    }

    /**
     * Checks if token is new line.
     *
     * @param Token $token
     *
     * @return bool
     */
    private function isNewLine(Token $token)
    {
        return trim($token->getContent(), " \r\t\0\x0B") === "\n";
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $expectedType
     *
     * @return bool
     */
    private function isFunction(Tokens $tokens, $index, $expectedType = T_FUNCTION)
    {
        return isset($tokens[$index]) && $tokens[$index]->isGivenKind($expectedType);
    }
}
