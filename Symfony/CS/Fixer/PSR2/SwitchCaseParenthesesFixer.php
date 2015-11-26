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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class SwitchCaseParenthesesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CASE)) {
                continue;
            }
            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            if (null === $nextTokenIndex || !$tokens[$nextTokenIndex]->equals('(')) {
                continue;
            }
            $blockEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextTokenIndex);
            $blockEndNextIndex = $tokens->getNextMeaningfulToken($blockEndIndex);
            if (!$tokens[$blockEndNextIndex]->equals(':')) {
                continue;
            }
            $tokens[$nextTokenIndex]->clear();
            $tokens[$blockEndIndex]->clear();
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes parentheses around switch cases.';
    }
}
