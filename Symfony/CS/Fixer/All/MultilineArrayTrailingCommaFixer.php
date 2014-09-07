<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class MultilineArrayTrailingCommaFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens->isArray($index)) {
                $this->fixArray($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP multi-line arrays should have a trailing comma.';
    }

    private function fixArray(Tokens $tokens, $index)
    {
        $bracesLevel = 0;

        $startIndex = $index;

        if ($tokens[$index]->isGivenKind(T_ARRAY)) {
            $tokens->getNextTokenOfKind($index, array('(', '['), $startIndex);
        }

        if (!$tokens->isArrayMultiLine($index)) {
            return;
        }

        if ($tokens[$startIndex]->equals('(')) {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        } else {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $startIndex);
        }

        $beforeEndIndex = null;
        $beforeEndToken = $tokens->getTokenNotOfKindSibling($endIndex, -1, array(array(T_WHITESPACE), array(T_COMMENT), array(T_DOC_COMMENT)), $beforeEndIndex);

        // if there is some item between braces then add `,` after it
        if ($startIndex !== $beforeEndIndex && !$beforeEndToken->equals(',')) {
            $tokens->insertAt($beforeEndIndex + 1, new Token(','));
        }
    }
}
