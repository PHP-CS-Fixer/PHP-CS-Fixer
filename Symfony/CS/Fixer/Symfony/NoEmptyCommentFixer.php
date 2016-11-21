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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoEmptyCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_COMMENT)) {
                $this->fixComment($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be any empty comments.';
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
                if ($this->isSurroundedBySingleLineComments($tokens, $index)) {
                    return;
                }

                $this->clearCommentToken($tokens, $index);
            }

            return;
        }

        // single line comment starting with '//'
        if ('/' === $content[1]) {
            if (preg_match('|^//\s*$|', $content)) {
                if ($this->isSurroundedBySingleLineComments($tokens, $index)) {
                    return;
                }

                $this->clearCommentToken($tokens, $index);
            }

            return;
        }

        // comment starting with '/*' and ending with '*/' (but not a PHPDoc)
        if (preg_match('|^/\*\s*\*/$|', $content)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }

    /**
     * Clear comment token, but preserve trailing linebreak if there is any.
     *
     * @param Tokens $tokens
     * @param int    $index  T_COMMENT index
     *
     * @deprecated Will be removed in the 2.0
     */
    private function clearCommentToken(Tokens $tokens, $index)
    {
        if ("\n" !== substr($tokens[$index]->getContent(), -1, 1)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return;
        }

        // if previous not-cleared token is whitespace;
        // append line break to content
        $previous = $tokens->getNonEmptySibling($index, -1);
        if ($tokens[$previous]->isWhitespace()) {
            $tokens[$previous]->setContent($tokens[$previous]->getContent()."\n");
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return;
        }

        // elseif the next not-cleared token is whitespace;
        // prepend with line break
        $next = $tokens->getNonEmptySibling($index, 1);
        if (null !== $next && $tokens[$next]->isWhitespace()) {
            $tokens[$next]->setContent("\n".$tokens[$next]->getContent());
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return;
        }

        // else
        // override with whitespace token linebreak
        $tokens->overrideAt($index, array(T_WHITESPACE, "\n", $tokens[$index]->getLine()));
    }

    /**
     * isSurroundedBySingleLineComments checks whether the preceding and
     * succeeding lines of the token at $index contain a single-line comment.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isSurroundedBySingleLineComments(Tokens $tokens, $index)
    {
        return $this->hasSingleLineCommentSibling($tokens, $index, -1) &&
               $this->hasSingleLineCommentSibling($tokens, $index,  1);
    }

    /**
     * hasSingleLineCommentSibling checks whether the preceding or succeeding
     * line of the token at $index contains a single-line comment. Which line
     * is checked depends on $direction.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $direction either -1 to check the preceding line, or +1 to
     *                          check the succeeding line
     *
     * @return bool
     */
    private function hasSingleLineCommentSibling(Tokens $tokens, $index, $direction)
    {
        do {
            $index += $direction;
            if (false === isset($tokens[$index])) {
                return false;
            }

            $token = $tokens[$index];
            $content = $token->getContent();
        } while (false === strpos($content, "\n"));

        if (false === $token->isComment()) {
            return false;
        }

        return '#' === $content[0] || '/' === $content[1];
    }
}
