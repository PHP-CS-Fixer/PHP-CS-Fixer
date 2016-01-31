<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform `class` class' constant from T_CLASS into CT_CLASS_CONSTANT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ClassConstantTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_CLASS_CONSTANT');
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$token->isGivenKind(T_CLASS)) {
            return;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        $prevToken = $tokens[$prevIndex];

        if ($prevToken->isGivenKind(T_DOUBLE_COLON)) {
            $token->override(array(CT_CLASS_CONSTANT, $token->getContent()));
        }
    }
}
