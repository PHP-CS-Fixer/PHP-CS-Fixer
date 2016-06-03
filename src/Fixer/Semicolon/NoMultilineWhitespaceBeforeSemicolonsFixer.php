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

namespace PhpCsFixer\Fixer\Semicolon;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NoMultilineWhitespaceBeforeSemicolonsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(';');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals(';')) {
                continue;
            }

            $previous = $tokens[$index - 1];
            if (!$previous->isWhitespace() || false === strpos($previous->getContent(), "\n")) {
                continue;
            }

            $content = $previous->getContent();
            if (("\n" === $content[0] || "\r" === $content[0]) && $tokens[$index - 2]->isComment()) {
                $previous->setContent("\r" === $content[0] ? "\r\n" : "\n");
            } else {
                $previous->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Multi-line whitespace before closing semicolon are prohibited.';
    }
}
