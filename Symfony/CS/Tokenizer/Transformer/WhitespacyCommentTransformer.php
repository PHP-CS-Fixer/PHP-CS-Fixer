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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Move trailing whitespaces from comments and docs into following T_WHITESPACE token.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class WhitespacyCommentTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isComment()) {
                continue;
            }

            if (preg_match("/^(.*)([ \t\r\n]+)$/", $token->getContent(), $matches)) {
                $token->setContent($matches[1]);

                if (isset($tokens[$index + 1]) && $tokens[$index + 1]->isGivenKind(T_WHITESPACE)) {
                    $tokens[$index + 1]->setContent($matches[2].$tokens[$index + 1]->getContent());
                } else {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, $matches[2])));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array();
    }
}
