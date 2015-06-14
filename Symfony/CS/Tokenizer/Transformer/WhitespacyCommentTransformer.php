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
final class WhitespacyCommentTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$token->isComment()) {
            return;
        }

        $content = $token->getContent();
        $trimmedContent = rtrim($content);

        // nothing trimmed, nothing to do
        if ($content === $trimmedContent) {
            return;
        }

        $whitespaces = substr($content, strlen($trimmedContent));

        $token->setContent($trimmedContent);

        if (isset($tokens[$index + 1]) && $tokens[$index + 1]->isGivenKind(T_WHITESPACE)) {
            $tokens[$index + 1]->setContent($whitespaces.$tokens[$index + 1]->getContent());
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, $whitespaces)));
        }
    }
}
