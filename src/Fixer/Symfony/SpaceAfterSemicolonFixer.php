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

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class SpaceAfterSemicolonFixer extends AbstractFixer
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
        for ($index = count($tokens) - 2; $index > 0; --$index) {
            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                if (!$tokens[$index + 1]->equalsAny(array(')', array(T_INLINE_HTML)))) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                }
            } elseif (
                isset($tokens[$index + 2])
                && !$tokens[$index + 1]->equals(array(T_WHITESPACE, ' '))
                && $tokens[$index + 1]->isWhitespace(" \t")
                && !$tokens[$index + 2]->isComment()
                && !$tokens[$index + 2]->equals(')')
            ) {
                $tokens[$index + 1]->setContent(' ');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Fix whitespace after a semicolon.';
    }
}
