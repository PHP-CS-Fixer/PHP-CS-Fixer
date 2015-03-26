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
 * @author Jared Henderson <jared@netrivet.com>
 */
class TrimArraySpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $c = $tokens->count(); $index < $c; ++$index) {
            if ($tokens[$index]->isGivenKind(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN))) {
                self::fixArray($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Arrays should be formatted like function/method arguments, without leading or trailing single line space.';
    }

    /**
     * Method to trim leading/trailing whitespace within single line arrays.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    private static function fixArray(Tokens $tokens, $index)
    {
        static $whitespaceOptions = array('whitespaces' => " \t");

        $startIndex = $index;

        if ($tokens[$startIndex]->isGivenKind(T_ARRAY)) {
            $startIndex = $tokens->getNextMeaningfulToken($startIndex);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        } else {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        }

        $nextToken = $tokens[$startIndex + 1];

        if ($nextToken->isWhitespace($whitespaceOptions)) {
            $nextToken->clear();
        }

        $prevToken = $tokens[$endIndex - 1];
        $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($endIndex)];

        if (
            $prevToken->isWhitespace($whitespaceOptions)
            && !$prevNonWhitespaceToken->equals(',')
        ) {
            $prevToken->clear();
        }
    }
}
