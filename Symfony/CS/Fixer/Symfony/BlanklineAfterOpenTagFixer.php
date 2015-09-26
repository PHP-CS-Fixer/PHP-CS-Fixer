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

        // ignore non-monolithic files
        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        // ignore files with short open tag
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG)) {
            return $content;
        }

        $newlineFound = false;
        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->isWhitespace(array('whitespaces' => "\n"))) {
                $newlineFound = true;
                break;
            }
        }

        // ignore one-line files
        if (!$newlineFound) {
            return $content;
        }

        $token = $tokens[0];

        if (false === strpos($token->getContent(), "\n")) {
            $token->setContent(rtrim($token->getContent())."\n");
        }

        if (!$tokens[1]->isWhitespace() && false === strpos($tokens[1]->getContent(), "\n")) {
            $tokens->insertAt(1, new Token(array(T_WHITESPACE, "\n")));
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
