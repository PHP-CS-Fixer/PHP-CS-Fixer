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

            $index = $tokens->getNextTokenOfKind($index, array(';', '{'));
            if ($tokens[$index]->equals('{')) {
                $this->fixFunction($tokens, $index, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index));
            }
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
        // should be run before ReturnFixer, ExtraEmptyLinesFixer, WhitespacyLinesFixer and after EmptyReturnFixer and DuplicateSemicolonFixer.
        return -18;
    }

    /**
     * @param Tokens $tokens
     * @param int    $start  Token index of the opening brace token of the function.
     * @param int    $end    Token index of the closing brace token of the function.
     */
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
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $start          Token index of the first token of the useless return.
     * @param int    $semiColonIndex Token index of the semicolon token ending the useless return statement.
     */
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
