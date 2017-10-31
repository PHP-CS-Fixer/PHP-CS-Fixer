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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform braced class instantiation braces in `(new Foo())` into CT::T_BRACE_CLASS_INSTANTIATION_OPEN
 * and CT::T_BRACE_CLASS_INSTANTIATION_CLOSE.
 *
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 */
final class BraceClassInstantiationTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens()
    {
        return array(CT::T_BRACE_CLASS_INSTANTIATION_OPEN, CT::T_BRACE_CLASS_INSTANTIATION_CLOSE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run after CurlyBraceTransformer and SquareBraceTransformer
        return -1;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 50000;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$tokens[$index]->equals('(') || !$tokens[$tokens->getNextMeaningfulToken($index)]->equals(array(T_NEW))) {
            return;
        }

        if ($tokens[$tokens->getPrevMeaningfulToken($index)]->equalsAny(array(
            array(T_STRING),
            ']',
            array(CT::T_ARRAY_SQUARE_BRACE_CLOSE),
            array(CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE),
            array(T_VARIABLE),
            array(T_CLASS),
            array(T_ARRAY),
        ))) {
            return;
        }

        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

        $tokens[$index] = new Token(array(CT::T_BRACE_CLASS_INSTANTIATION_OPEN, '('));
        $tokens[$closeIndex] = new Token(array(CT::T_BRACE_CLASS_INSTANTIATION_CLOSE, ')'));
    }
}
