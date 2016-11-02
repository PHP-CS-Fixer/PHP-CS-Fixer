<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fix spacing around semicolons in `for` control structures as defined in PSR2 ¶5.4.
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 */
final class ForLoopSemicolonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($i = count($tokens) - 1; 0 <= $i; --$i) {
            if ($tokens[$i]->isGivenKind(T_FOR)) {
                $this->fixLoopSemicolonSpacing($tokens, $i);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Spacing around semicolons in `for` control structures.';
    }

    private function fixLoopSemicolonSpacing(Tokens $tokens, $index)
    {
        $expressionStart = $tokens->getNextTokenOfKind($index, array('('));
        $expressionEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $expressionStart);
        $this->fixSemicolonSpacing($tokens, $expressionStart, $expressionEnd);
    }

    private function fixSemicolonSpacing(Tokens $tokens, $expressionStart, $expressionEnd)
    {
        for ($index = $expressionEnd - 1; $index > $expressionStart; --$index) {
            if (!$tokens[$index]->equals(';') || $index === $expressionEnd - 1) {
                continue;
            }

            if ($tokens[$index + 1]->isWhitespace()) {
                if (!$tokens[$index + 1]->equals(' ')) {
                    $tokens[$index + 1]->setContent(' ');
                }
            } else {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            if ($tokens[$index - 1]->isWhitespace() && !$tokens[$index - 2]->equals(';')) {
                $tokens[$index - 1]->clear();
                --$index;
            }
        }
    }
}
