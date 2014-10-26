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

use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\AbstractTransformer;

/**
 * Transform curly braces in `$foo->{$bar}` into CT_DYNAMIC_PROP_BRACE_OPEN and CT_DYNAMIC_PROP_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class DynamicPropBrace extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_OBJECT_OPERATOR) as $index => $token) {
            if (!$tokens[$index + 1]->equals('{')) {
                continue;
            }

            $openIndex = $index + 1;
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);

            $tokens[$openIndex]->override(array(CT_DYNAMIC_PROP_BRACE_OPEN, '{', $tokens[$openIndex]->getLine()));
            $tokens[$closeIndex]->override(array(CT_DYNAMIC_PROP_BRACE_CLOSE, '}', $tokens[$closeIndex]->getLine()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_DYNAMIC_PROP_BRACE_OPEN', 'CT_DYNAMIC_PROP_BRACE_CLOSE');
    }
}
