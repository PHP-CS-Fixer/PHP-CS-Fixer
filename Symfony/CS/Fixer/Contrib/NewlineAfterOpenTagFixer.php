<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
class NewlineAfterOpenTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_OPEN_TAG)) {
                break;
            }

            if (false !== strpos($token->getContent(), "\n")) {
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

            $token->setContent(rtrim($token->getContent())."\n");
            break;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ensure there is no code on the same line as the PHP open tag.';
    }
}
