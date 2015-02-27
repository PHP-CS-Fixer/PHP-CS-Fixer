<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
class BlanklineAfterOpenTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_OPEN_TAG)) {
                break;
            }

            $newlines = 0;
            $whitespaceTokens = $tokens->findGivenKind(T_WHITESPACE);
            foreach ($whitespaceTokens as $whitespaceToken) {
                if ($whitespaceToken->isWhitespace(array('whitespaces' => "\n"))) {
                    ++$newlines;
                }
            }
            if (0 === $newlines) {
                break;
            }

            if (false === strpos($token->getContent(), "\n")) {
                $token->setContent(rtrim($token->getContent())."\n");
            }

            if (!$tokens[$index + 1]->isWhitespace(array('whitespaces' => "\n"))) {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, "\n")));
            }
            break;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ensure there is no code on the same line as the PHP open tag and it is followed by a blankline.';
    }
}
