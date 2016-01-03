<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NoShortBoolCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('!');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            if ($tokens[$index]->equals('!')) {
                $index = $this->fixShortCast($tokens, $index);
            }
        }
    }

    private function fixShortCast(Tokens $tokens, $index)
    {
        for ($i = $index - 1; $i > 1; --$i) {
            if ($tokens[$i]->equals('!')) {
                $this->fixShortCastToBoolCast($tokens, $i, $index);
                break;
            }

            if (!$tokens[$i]->isComment() && !$tokens[$i]->isWhitespace()) {
                break;
            }
        }

        return $i;
    }

    private function fixShortCastToBoolCast(Tokens $tokens, $start, $end)
    {
        for (; $start <= $end; ++$start) {
            if (
                !$tokens[$start]->isComment()
                && !($tokens[$start]->isWhitespace() && $tokens[$start - 1]->isComment())
            ) {
                $tokens[$start]->clear();
            }
        }

        $tokens->insertAt($start, new Token(array(T_BOOL_CAST, '(bool)')));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Short cast bool using double exclamation mark should not be used.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the SpacesCastFixer
        return -9;
    }
}
