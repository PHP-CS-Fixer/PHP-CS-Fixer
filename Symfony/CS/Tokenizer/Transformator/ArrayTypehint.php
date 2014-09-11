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

use Symfony\CS\Token;
use Symfony\CS\Tokens;
use Symfony\CS\Tokenizer\AbstractTransformator;

/**
 * Transform `array` typehint from T_ARRAY to T_ARRAY_TYPEHINT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ArrayTypehint extends AbstractTransformator
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_ARRAY)) {
                continue;
            }

            $nextIndex = $tokens->getTokenNotOfKindSibling($index, 1, array(array(T_WHITESPACE), array(T_COMMENT), array(T_DOC_COMMENT)));
            $nextToken = $tokens[$nextIndex];

            if (!$nextToken->equals('(')) {
                $token->id = T_ARRAY_TYPEHINT;
            }
        }
    }

    public function getConstantDefinitions()
    {
        static $defs = array(10001 => 'T_ARRAY_TYPEHINT');

        return $defs;
    }
}
