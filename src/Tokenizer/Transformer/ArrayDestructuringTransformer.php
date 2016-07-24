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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform destructuring brace tokens in `[$a, $b, $c] = [1, 2, 3]`.
 *
 * Performed transformations:
 * - CT_DESTRUCTURING_SQUARE_BRACE_OPEN for [,
 * - CT_DESTRUCTURING_SQUARE_BRACE_CLOSE for ].
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class ArrayDestructuringTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array(
            'CT_DESTRUCTURING_SQUARE_BRACE_CLOSE',
            'CT_DESTRUCTURING_SQUARE_BRACE_OPEN',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 70100;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        // TODO
    }
}
