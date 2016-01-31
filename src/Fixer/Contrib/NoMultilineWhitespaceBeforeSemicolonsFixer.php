<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
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

            if ($previous->isWhitespace() && false !== strpos($previous->getContent(), "\n")) {
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
