<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformer;

use Symfony\CS\Tokenizer\AbstractTransformer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform closing `}` for T_DOLLAR_OPEN_CURLY_BRACES into CT_DOLLAR_CLOSE_CURLY_BRACES.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class DollarCloseCurlyBraces extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_DOLLAR_OPEN_CURLY_BRACES) as $index => $token) {
            $nextIndex = $tokens->getNextTokenOfKind($index, array('}'));
            $tokens[$nextIndex]->override(array(CT_DOLLAR_CLOSE_CURLY_BRACES, '}', $tokens[$nextIndex]->getLine()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_DOLLAR_CLOSE_CURLY_BRACES');
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
