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
 * Transform short array braces into:
 * - CT_ARRAY_SQUARE_BRACE_OPEN for [,
 * - CT_ARRAY_SQUARE_BRACE_CLOSE for ].
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class ArraySquareBraceTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_ARRAY_SQUARE_BRACE_OPEN', 'CT_ARRAY_SQUARE_BRACE_CLOSE');
    }
}
