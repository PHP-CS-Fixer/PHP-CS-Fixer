<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EofEndingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $count = $tokens->count();
        if (0 === $count) {
            return '';
        }

        $token = $tokens[$count - 1];
        if ($token->isGivenKind(array(T_INLINE_HTML, T_CLOSE_TAG, T_OPEN_TAG))) {
            return $content;
        }

        if ($token->isWhitespace()) {
            $lineBreak = false === strrpos($token->getContent(), "\r") ? "\n" : "\r\n";
            $token->setContent($lineBreak);
        } else {
            $tokens->insertAt($count, new Token(array(T_WHITESPACE, "\n")));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A file must always end with a single empty line feed.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run last to be sure the file is properly formatted before it runs
        return -50;
    }
}
