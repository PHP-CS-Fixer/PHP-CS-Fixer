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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jared Henderson <jared@netrivet.com>
 */
final class TrimArraySpacesFixer extends AbstractFixer
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
        $startIndex = $index;

        if ($tokens[$startIndex]->isGivenKind(T_ARRAY)) {
            $startIndex = $tokens->getNextMeaningfulToken($startIndex);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        } else {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        }

        $nextToken = $tokens[$startIndex + 1];
        $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($startIndex);
        $nextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex];
        $tokenAfterNextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex + 1];

        $prevToken = $tokens[$endIndex - 1];
        $prevNonWhitespaceIndex = $tokens->getPrevNonWhitespace($endIndex);
        $prevNonWhitespaceToken = $tokens[$prevNonWhitespaceIndex];

        if (
            $nextToken->isWhitespace(" \t")
            && (
                !$nextNonWhitespaceToken->isComment()
                || $nextNonWhitespaceIndex === $prevNonWhitespaceIndex
                || $tokenAfterNextNonWhitespaceToken->isWhitespace(" \t")
                || '/*' === substr($nextNonWhitespaceToken->getContent(), 0, 2)
            )
        ) {
            $nextToken->clear();
        }

        if (
            $prevToken->isWhitespace(" \t")
            && !$prevNonWhitespaceToken->equals(',')
        ) {
            $prevToken->clear();
        }
    }
}
