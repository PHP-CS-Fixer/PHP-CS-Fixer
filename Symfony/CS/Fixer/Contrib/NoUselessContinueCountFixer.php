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
final class NoUselessContinueCountFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(array(T_CONTINUE, T_BREAK))) {
                $this->fixContinueBreak($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Continue and break statements should not use 0 or 1.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // Should be run before the SpacesAfterSemicolonFixer, WhitespacyLinesFixer, ExtraEmptyLinesFixer and TrailingSpacesFixer.
        // Should run after UnneededControlParenthesesFixer.
        return 1;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  Token index of T_CONTINUE, T_BREAK.
     */
    private function fixContinueBreak(Tokens $tokens, $index)
    {
        $next = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$next]->isGivenKind(T_LNUMBER)) {
            return;
        }

        $afterNumber = $tokens->getNextMeaningfulToken($next);
        if (!$tokens[$afterNumber]->equals(';')) {
            return;
        }

        $c = $tokens[$next]->getContent();
        if ('0' === $c || '1' === $c) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($next);
        }
    }
}
