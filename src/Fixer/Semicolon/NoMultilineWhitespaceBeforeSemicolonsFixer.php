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
use PhpCsFixer\WhitespacesFixerConfigAwareInterface;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NoMultilineWhitespaceBeforeSemicolonsFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
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
        $lineEnding = $this->whitespacesConfig->getLineEnding();

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
                $previous->setContent($lineEnding);
            } else {
                $previous->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Multi-line whitespace before closing semicolon are prohibited.';
    }
}
