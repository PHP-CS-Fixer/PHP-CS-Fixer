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

namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
final class BlankLineAfterOpeningTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OPEN_TAG);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // ignore non-monolithic files
        if (!$tokens->isMonolithicPhp()) {
            return;
        }

        // ignore files with short open tag
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG)) {
            return;
        }

        $newlineFound = false;
        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->isWhitespace("\n")) {
                $newlineFound = true;
                break;
            }
        }

        // ignore one-line files
        if (!$newlineFound) {
            return;
        }

        $token = $tokens[0];

        if (false === strpos($token->getContent(), "\n")) {
            $token->setContent(rtrim($token->getContent())."\n");
        }

        if (!$tokens[1]->isWhitespace() && false === strpos($tokens[1]->getContent(), "\n")) {
            $tokens->insertAt(1, new Token(array(T_WHITESPACE, "\n")));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ensure there is no code on the same line as the PHP open tag and it is followed by a blankline.';
    }
}
