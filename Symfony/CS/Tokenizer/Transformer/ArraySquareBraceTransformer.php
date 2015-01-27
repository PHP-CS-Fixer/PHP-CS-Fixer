<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformer;

use Symfony\CS\Tokenizer\AbstractTransformer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform discriminate overloaded square braces tokens.
 *
 * Performed transformations:
 * - CT_ARRAY_SQUARE_BRACE_OPEN for [,
 * - CT_ARRAY_SQUARE_BRACE_CLOSE for ].
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class ArraySquareBraceTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            if (!$this->isShortArray($tokens, $index)) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);

            $tokens[$index]->override(array(CT_ARRAY_SQUARE_BRACE_OPEN, '['));
            $tokens[$endIndex]->override(array(CT_ARRAY_SQUARE_BRACE_CLOSE, ']'));
        }
    }

    /**
     * Check if token under given index is short array opening.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isShortArray(Tokens $tokens, $index)
    {
        static $allowedPrevTokens = array(
            array(T_DOUBLE_ARROW),
            array(T_RETURN),
            array(CT_ARRAY_SQUARE_BRACE_OPEN),
            '=',
            '+',
            ',',
            '(',
            '[',
        );

        $token = $tokens[$index];

        if (!$token->equals('[')) {
            return false;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if ($prevToken->equalsAny($allowedPrevTokens)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_ARRAY_SQUARE_BRACE_OPEN', 'CT_ARRAY_SQUARE_BRACE_CLOSE');
    }
}
