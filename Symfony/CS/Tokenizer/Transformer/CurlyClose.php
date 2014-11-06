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
 * Transform closing `}` for T_CURLY_OPEN into CT_CURLY_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class CurlyClose extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_CURLY_OPEN) as $index => $token) {
            $level = 1;
            $nestIndex = $index;

            while (0 < $level) {
                ++$nestIndex;

                // we count all kind of {
                if ('{' === $tokens[$nestIndex]->getContent()) {
                    ++$level;
                    continue;
                }

                // we count all kind of }
                if ('}' === $tokens[$nestIndex]->getContent()) {
                    --$level;
                }
            }

            $tokens[$nestIndex]->override(array(CT_CURLY_CLOSE, '}', $tokens[$nestIndex]->getLine()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_CURLY_CLOSE');
    }
}
