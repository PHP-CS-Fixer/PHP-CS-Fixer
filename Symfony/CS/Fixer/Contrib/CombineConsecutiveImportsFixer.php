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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class CombineConsecutiveImportsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($index = 0, $count = count($tokens); $index < $count - 1; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_GLOBAL)) {
                continue;
            }

            // go to end of global statement
            $lastOfGlobalStatement = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));

            // do not merge global imports if there is closing tag between those
            if ($tokens[$lastOfGlobalStatement]->isGivenKind(T_CLOSE_TAG)) {
                $index = $lastOfGlobalStatement;

                continue;
            }

            // find next global candidate to merge
            $nextGlobalStatementStart = $tokens->getNextMeaningfulToken($lastOfGlobalStatement);
            if (null === $nextGlobalStatementStart) {
                break;
            }

            if (!$tokens[$nextGlobalStatementStart]->isGivenKind(T_GLOBAL)) {
                $index = $nextGlobalStatementStart;

                continue;
            }

            $nextGlobalStatementEnd = $tokens->getNextTokenOfKind($nextGlobalStatementStart, array(';', array(T_CLOSE_TAG)));

            // merge statements
            $tokens->insertAt($lastOfGlobalStatement, new Token(','));
            $tokens->insertAt($lastOfGlobalStatement + 1, new Token(array(T_WHITESPACE, ' ')));

            $added = $this->moveTokens(
                $tokens,
                $nextGlobalStatementStart + 3, // + 1 because we don't want the `global` token, + 2 because we inserted two tokens
                $nextGlobalStatementEnd + 2,   // + 2 because we inserted two tokens
                $lastOfGlobalStatement + 1     // + 2 because we inserted two tokens, -1 to be before the closing token
            );

            $added += 2; // because we inserted two tokens

            // clear the second `global` and end of statement token
            $tokens->clearTokenAndMergeSurroundingWhitespace($added + $nextGlobalStatementStart);
            if ($tokens[$added + $nextGlobalStatementEnd]->equals(';')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($added + $nextGlobalStatementEnd);
            }

            --$index;
            $count += $added;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Calling import on multiple variables should be done in one call on one line.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should ran before ExtraEmptyLinesFixer, SpacesBeforeSemicolonFixer, TrailingSpacesFixer and WhitespacyLinesFixer.
        return 25; // FIXME
    }

    /**
     * @param Tokens $tokens
     * @param int    $start  Index previous of the first token to move
     * @param int    $end    Index of the last token to move
     * @param int    $to     Upper boundary index
     *
     * @return int Number of tokens inserted
     */
    private function moveTokens(Tokens $tokens, $start, $end, $to)
    {
        $added = 0;
        for ($i = $start + 1; $i < $end; $i += 2) {
            if ($tokens[$i]->isWhitespace() && $tokens[$to + 1]->isWhitespace()) {
                $tokens[$to + 1]->setContent($tokens[$to + 1]->getContent().$tokens[$i]->getContent());
            } else {
                $tokens->insertAt(++$to, clone $tokens[$i]);
                ++$end;
                ++$added;
            }

            $tokens[$i + 1]->clear();
        }

        return $added;
    }
}
