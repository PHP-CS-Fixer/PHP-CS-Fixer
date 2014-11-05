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
 * Transform `class` class' constant from T_CLASS into CT_CLASS_CONSTANT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ClassConstant extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_CLASS) as $index => $token) {
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];

            if ($prevToken->isGivenKind(T_DOUBLE_COLON)) {
                $token->override(array(CT_CLASS_CONSTANT, $token->getContent(), $token->getLine()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_CLASS_CONSTANT');
    }
}
