<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformator;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\AbstractTransformator;

/**
 * Transform closing `}` for T_DOLLAR_OPEN_CURLY_BRACES into CT_DOLLAR_CLOSE_CURLY_BRACES.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class DollarCloseCurlyBraces extends AbstractTransformator
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_DOLLAR_OPEN_CURLY_BRACES) as $index => $token) {
            $nextIndex = $tokens->getNextTokenOfKind($index, array('}'));
            $tokens[$nextIndex] = new Token(array(CT_DOLLAR_CLOSE_CURLY_BRACES, '}', $tokens[$nextIndex]->line));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_DOLLAR_CLOSE_CURLY_BRACES');
    }
}
