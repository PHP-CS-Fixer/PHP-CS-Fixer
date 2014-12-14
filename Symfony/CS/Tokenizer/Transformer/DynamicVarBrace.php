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
 * Transform curly braces in `${$foo}` into CT_DYNAMIC_VAR_BRACE_OPEN and CT_DYNAMIC_VAR_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class DynamicVarBrace extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('$')) {
                continue;
            }

            $openIndex = $tokens->getNextMeaningfulToken($index);

            if (null === $openIndex) {
                continue;
            }

            $openToken = $tokens[$openIndex];

            if (!$openToken->equals('{')) {
                continue;
            }

            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);
            $closeToken = $tokens[$closeIndex];

            $openToken->override(array(CT_DYNAMIC_VAR_BRACE_OPEN, '{', $openToken->getLine()));
            $closeToken->override(array(CT_DYNAMIC_VAR_BRACE_CLOSE, '}', $closeToken->getLine()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_DYNAMIC_VAR_BRACE_OPEN', 'CT_DYNAMIC_VAR_BRACE_CLOSE');
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the CurlyClose
        return -10;
    }
}
