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
 * @author SpacePossum
 */
final class SpacesAfterSemicolonFixer extends AbstractFixer
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
        for ($index = count($tokens) - 3; $index > 0; --$index) {
            if (!$tokens[$index]->equals(';') || $tokens[$index + 2]->isComment()) {
                continue;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            } elseif (!$tokens[$index + 1]->equals(array(T_WHITESPACE, ' ')) && $tokens[$index + 1]->isWhitespace(" \t")) {
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
