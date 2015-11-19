<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Adam Marczuk <adam@marczuk.info>
 */
final class ArrayElementNoSpaceBeforeCommaFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN))) {
                $this->fixSpacing($index, $tokens);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In array declaration, there MUST NOT be a space before each comma.';
    }

    /**
     * Method to fix spacing in array declaration.
     *
     * @param int    $index
     * @param Tokens $tokens
     */
    private function fixSpacing($index, Tokens $tokens)
    {
        if ($tokens[$index]->isGivenKind(CT_ARRAY_SQUARE_BRACE_OPEN)) {
            $startIndex = $index;
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        } else {
            $startIndex = $tokens->getNextTokenOfKind($index, array('('));
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        }

        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $i = $this->skipNonArrayElements($i, $tokens);
            $currentToken = $tokens[$i];
            $prevIndex = $tokens->getPrevNonWhitespace($i - 1);
            if ($currentToken->equals(',') && !$tokens[$prevIndex]->equals(array(T_END_HEREDOC))) {
                $tokens->removeLeadingWhitespace($i);
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
        if ($tokens[$index]->equals('}')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index, false);
        }

        if ($tokens[$index]->equals(')')) {
            $startIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);
            $startIndex = $tokens->getPrevMeaningfulToken($startIndex);
            if (!$tokens[$startIndex]->isGivenKind(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN))) {
                return $startIndex;
            }
        }

        return $index;
    }
}
