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

        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

        if (!$tokens[$tokens->getNextMeaningfulToken($closeIndex)]->isGivenKind(array(T_OBJECT_OPERATOR, T_DOUBLE_COLON))) {
            return;
        }

        $tokens[$index] = new Token(array(CT::T_BRACE_CLASS_INSTANTIATION_OPEN, '('));
        $tokens[$closeIndex] = new Token(array(CT::T_BRACE_CLASS_INSTANTIATION_CLOSE, ')'));
    }
}
