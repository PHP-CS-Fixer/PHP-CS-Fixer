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
 * @author Adam Marczuk <adam@marczuk.info>
 */
class ArrayElementSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens->isArray($index)) {
                continue;
            }

            $this->fixSpacing($index, $tokens);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In array declaration, there MUST NOT be a space before each comma, there MUST be one space after each comma and there should be space around double arrow.';
    }

    /**
     * Method to fix spacing in array declaration.
     *
     * @param int    $index
     * @param Tokens $tokens
     */
    private function fixSpacing($index, Tokens $tokens)
    {
        $multiLine = $tokens->isArrayMultiLine($index);
        if ($tokens->isShortArray($index)) {
            $startIndex = $index;
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $startIndex);
        } else {
            $startIndex = $tokens->getNextTokenOfKind($index, array('('));
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        }

        if ($tokens[$startIndex + 1]->isWhitespace()) {
            if (!$multiLine || ($multiLine && false === strpos($tokens[$startIndex + 1]->getContent(), "\n"))) {
                $tokens[$startIndex + 1]->clear();
            }
        }
        if ($tokens[$endIndex - 1]->isWhitespace()) {
            if (!$multiLine || ($multiLine && false === strpos($tokens[$endIndex - 1]->getContent(), "\n"))) {
                $tokens[$endIndex - 1]->clear();
            }
        }

        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $i = $this->skipNonArrayElements($i, $tokens);
            $currentToken = $tokens[$i];
            if ($currentToken->equals(',')) {
                $this->fixCommaSpace($i, $tokens, $multiLine);
            }
            if ($currentToken->isGivenKind(T_DOUBLE_ARROW)) {
                $this->fixDoubleArrowSpace($i, $tokens);
            }
        }
    }

    /**
     * Method to move index over the non-array elements like function calls or function declarations.
     *
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return int New index
     */
    private function skipNonArrayElements($index, Tokens $tokens)
    {
        if ($tokens[$index]->equals(')')) {
            $startIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);
            $startIndex = $tokens->getPrevMeaningfulToken($startIndex);
            if (!$tokens->isArray($startIndex)) {
                return $startIndex;
            }
        } elseif ($tokens[$index]->equals('}')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index, false);
        }

        return $index;
    }

    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param int    $index
     * @param Tokens $tokens
     * @param bool   $multiLine
     */
    private function fixCommaSpace($index, Tokens $tokens, $multiLine)
    {
        if ($tokens[$index + 1]->isWhitespace()) {
            if (!$multiLine) {
                $tokens[$index + 1]->override(array(T_WHITESPACE, ' '));
            }
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }

        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens[$index - 1]->clear();
        }
    }

    /**
     * Method to ensure space around double arrow.
     *
     * @param int    $index
     * @param Tokens $tokens
     */
    private function fixDoubleArrowSpace($index, Tokens $tokens)
    {
        if ($tokens[$index + 1]->isWhitespace()) {
            $tokens[$index + 1]->override(array(T_WHITESPACE, ' '));
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }

        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens[$index - 1]->override(array(T_WHITESPACE, ' '));
        } else {
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
        }
    }
}
