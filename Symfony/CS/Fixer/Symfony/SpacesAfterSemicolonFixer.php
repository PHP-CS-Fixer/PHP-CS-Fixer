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
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 1, $count = count($tokens); $index < $count - 2; ++$index) {

            // handle the exception case for the last `;` inside a `for` loop
            if ($tokens[$index]->isGivenKind(T_FOR)) {
                $index = $this->fixSemicolonsInFor($tokens, $index);
                $count = count($tokens);
                continue;
            }

            if (!$tokens[$index]->equals(';') || ($index < $count - 3 && $tokens[$index + 1]->isWhitespace() && $tokens[$index + 2]->isComment())) {
                continue;
            }

            if ($this->ensureSingleSpaceAfter($tokens, $index)) {
                ++$count;
                ++$index;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Fix whitespace after a semicolon.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int 0|1 number of tokens inserted
     */
    private function ensureSingleSpaceAfter(Tokens $tokens, $index)
    {
        if (!$tokens[$index + 1]->isWhitespace()) {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));

            return 1;
        }

        if (!$tokens[$index + 1]->equals(array(T_WHITESPACE, ' ')) && $tokens[$index + 1]->isWhitespace(array('whitespaces' => " \t"))) {
            $tokens[$index + 1]->setContent(' ');
        }

        return 0;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function fixSemicolonsInFor(Tokens $tokens, $index)
    {
        $nextSemicolon = $tokens->getNextTokenOfKind($index, array(';'));
        $added = $this->ensureSingleSpaceAfter($tokens, $nextSemicolon);

        $forEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextMeaningfulToken($index));

        $nextSemicolon = $tokens->getNextTokenOfKind($nextSemicolon + $added, array(';'));
        $diff = $forEnd - $nextSemicolon;

        if (2 === $diff) {
            if ($tokens[$forEnd - 1]->isWhitespace()) {
                $tokens->removeTrailingWhitespace($nextSemicolon); // if ';[whitespace])' clear whitespace
            }
        } elseif (1 !== $diff) {
            $added = $this->ensureSingleSpaceAfter($tokens, $nextSemicolon); // if not `;)` than ensure a space
        }

        return $forEnd + $added;
    }
}
