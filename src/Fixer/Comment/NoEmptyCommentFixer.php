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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoEmptyCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_COMMENT)) {
                $this->fixComment($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be an empty comments.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after PhpdocToCommentFixer and before ExtraEmptyLinesFixer, TrailingSpacesFixer and WhitespacyLinesFixer.
        return 2;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_COMMENT index
     */
    private function fixComment(Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();

        // single line comment starting with '#'
        if ('#' === $content[0]) {
            if (preg_match('|^#\s*$|', $content)) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }

            return;
        }

        // single line comment starting with '//'
        if ('/' === $content[1]) {
            if (preg_match('|^//\s*$|', $content)) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }

            return;
        }

        // comment starting with '/*' and ending with '*/' (but not a PHPDoc)
        if (preg_match('|^/\*\s*\*/$|', $content)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }
}
