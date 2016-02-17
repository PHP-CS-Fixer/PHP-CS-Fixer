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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoWhileAsForLoopFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FOR)) {
                continue;
            }

            $this->fixForLoop($tokens, $index);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Simple for-loops should be written as while-loops.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_FOR index
     */
    private function fixForLoop(Tokens $tokens, $index)
    {
        $forConditionOpen = $tokens->getNextTokenOfKind($index, array('('));
        $firstSemicolon = $tokens->getNextMeaningfulToken($forConditionOpen);
        if (!$tokens[$firstSemicolon]->equals(';')) {
            return;
        }

        $secondSemicolon = $tokens->getNextTokenOfKind($firstSemicolon, array(';'));
        $forConditionClose = $tokens->getNextMeaningfulToken($secondSemicolon);
        if (!$tokens[$forConditionClose]->equals(')')) {
            return;
        }

        $tokens->overrideAt($index, array(T_WHILE, 'while', $tokens[$index]->getLine()));
        $tokens->clearTokenAndMergeSurroundingWhitespace($firstSemicolon);
        $tokens->clearTokenAndMergeSurroundingWhitespace($secondSemicolon);
    }
}
