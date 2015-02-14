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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform braced class instantiation braces in `(new Foo())` into CT_BRACE_CLASS_INSTANTIATION_OPEN
 * and CT_BRACE_CLASS_INSTANTIATION_CLOSE.
 *
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 */
class BraceClassInstantiationTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_BRACE_CLASS_INSTANTIATION_OPEN', 'CT_BRACE_CLASS_INSTANTIATION_CLOSE');
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

        $tokens[$index]->override(array(CT_BRACE_CLASS_INSTANTIATION_OPEN, '('));
        $tokens[$closeIndex]->override(array(CT_BRACE_CLASS_INSTANTIATION_CLOSE, ')'));
    }
}
