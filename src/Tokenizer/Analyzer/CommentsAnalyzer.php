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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class CommentsAnalyzer
{
    const TYPE_HASH = 1;
    const TYPE_DOUBLE_SLASH = 2;
    const TYPE_SLASH_ASTERISK = 3;

    /**
     * Return array of indices that are part of a comment started at given index.
     *
     * @param Tokens $tokens
     * @param int    $index  T_COMMENT index
     *
     * @return null|array
     */
    public function getCommentBlockIndices(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_COMMENT)) {
            throw new \InvalidArgumentException('Given index must point to a comment.');
        }

        $commentType = $this->getCommentType($tokens[$index]->getContent());
        $indices = [$index];

        if (self::TYPE_SLASH_ASTERISK === $commentType) {
            return $indices;
        }

        $count = count($tokens);
        ++$index;

        for (; $index < $count; ++$index) {
            if ($tokens[$index]->isComment()) {
                if ($commentType === $this->getCommentType($tokens[$index]->getContent())) {
                    $indices[] = $index;

                    continue;
                }

                break;
            }

            if (!$tokens[$index]->isWhitespace() || $this->getLineBreakCount($tokens, $index, $index + 1) > 1) {
                break;
            }
        }

        return $indices;
    }

    /**
     * @param string $content
     *
     * @return int
     */
    private function getCommentType($content)
    {
        if ('#' === $content[0]) {
            return self::TYPE_HASH;
        }

        if ('*' === $content[1]) {
            return self::TYPE_SLASH_ASTERISK;
        }

        return self::TYPE_DOUBLE_SLASH;
    }

    /**
     * @param Tokens $tokens
     * @param int    $whiteStart
     * @param int    $whiteEnd
     *
     * @return int
     */
    private function getLineBreakCount(Tokens $tokens, $whiteStart, $whiteEnd)
    {
        $lineCount = 0;
        for ($i = $whiteStart; $i < $whiteEnd; ++$i) {
            $lineCount += preg_match_all('/\R/u', $tokens[$i]->getContent());
        }

        return $lineCount;
    }
}
