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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

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
final class ArraySquareBraceTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_ARRAY_SQUARE_BRACE_OPEN', 'CT_ARRAY_SQUARE_BRACE_CLOSE');
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$this->isShortArray($tokens, $index)) {
            return;
        }

        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);

        $token->override(array(CT_ARRAY_SQUARE_BRACE_OPEN, '['));
        $tokens[$endIndex]->override(array(CT_ARRAY_SQUARE_BRACE_CLOSE, ']'));
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
        static $disallowedPrevTokens = array(
            ')',
            ']',
            '}',
            '"',
            array(T_CONSTANT_ENCAPSED_STRING),
            array(T_STRING),
            array(T_STRING_VARNAME),
            array(T_VARIABLE),
            array(CT_ARRAY_SQUARE_BRACE_CLOSE),
            array(CT_DYNAMIC_PROP_BRACE_CLOSE),
            array(CT_DYNAMIC_VAR_BRACE_CLOSE),
            array(CT_ARRAY_INDEX_CURLY_BRACE_CLOSE),
        );

        $token = $tokens[$index];

        if (!$token->equals('[')) {
            return false;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if (!$prevToken->equalsAny($disallowedPrevTokens)) {
            return true;
        }

        return false;
    }
}
