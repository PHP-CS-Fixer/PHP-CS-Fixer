<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
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
final class UselessReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $index = $functionOpen = $tokens->getNextTokenOfKind($index, array('{'));
            $this->fixFunction($tokens, $functionOpen, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $functionOpen));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be an empty return statement at the end of a function.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before ReturnFixer, ExtraEmptyLinesFixer, WhitespacyLinesFixer and after EmptyReturnFixer
        return -18;
    }

    private function fixFunction(Tokens $tokens, $start, $end)
    {
        for ($index = $end; $index > $start; --$index) {
            if (!$tokens[$index]->isGivenKind(T_RETURN)) {
                continue;
            }

            $nextAt = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$nextAt]->equals(';')) {
                continue;
            }

            if ($tokens->getNextMeaningfulToken($nextAt) !== $end) {
                continue;
            }

            $this->removeReturnStatement($tokens, $index, $nextAt);

            return;
        }
    }

    private function removeReturnStatement(Tokens $tokens, $start, $semiColonIndex)
    {
        $commentFound = false;
        for ($index = $semiColonIndex; $index >= $start; --$index) {
            if ($tokens[$index]->isComment()) {
                $commentFound = true;
                continue;
            }

            $tokens[$index]->clear();
        }

        // merge whitespace tokens if needed
        if (!$commentFound && $tokens[$start - 1]->isWhitespace() && $tokens[$semiColonIndex + 1]->isWhitespace()) {
            $tokens[$start - 1]->setContent($tokens[$start - 1]->getContent().$tokens[$semiColonIndex + 1]->getContent());
            $tokens[$semiColonIndex + 1]->clear();
        }
    }
}
