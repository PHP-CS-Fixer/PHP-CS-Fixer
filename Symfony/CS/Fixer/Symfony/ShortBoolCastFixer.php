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

class ShortBoolCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            if ($tokens[$index]->equals('!')) {
                $index = $this->fixShortCast($tokens, $index);
            }
        }

        return $tokens->generateCode();
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
            if (!$tokens[$start]->isComment()) {
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
