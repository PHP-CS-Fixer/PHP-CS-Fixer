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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author ntzm
 */
final class NoEmptyBlockFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must not be any empty blocks.',
            [
                new CodeSample("<?php if (\$foo) {}\n"),
                new CodeSample("<?php switch (\$foo) {}\n"),
                new CodeSample("<?php while (\$foo) {}\n"),
            ],
            null,
            'Risky if the block has side effects'
        );
    }

    public function isRisky()
    {
        return true;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([
            T_ELSE,
            T_FINALLY,
            T_FOR,
            T_IF,
            T_SWITCH,
            T_TRY,
            T_WHILE,
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_ELSE)) {
                $this->fixElse($index, $tokens);
            } elseif ($token->isGivenKind(T_DO)) {
                $this->fixDoWhile($index, $tokens);
            } elseif ($token->isGivenKind(T_FOR)) {
                $this->fixFor($index, $tokens);
            } elseif ($token->isGivenKind(T_FINALLY)) {
                $this->fixFinally($index, $tokens);
            } elseif ($token->isGivenKind(T_IF)) {
                $this->fixIf($index, $tokens);
            } elseif ($token->isGivenKind(T_SWITCH)) {
                $this->fixSwitch($index, $tokens);
            } elseif ($token->isGivenKind(T_TRY)) {
                $this->fixTry($index, $tokens);
            } elseif ($token->isGivenKind(T_WHILE)) {
                $this->fixWhile($index, $tokens);
            }
        }
    }

    /**
     * @param int    $elseIndex
     * @param Tokens $tokens
     */
    private function fixElse($elseIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($elseIndex);

        if ($tokens[$openBodyIndex]->equals(':')) {
            $endifIndex = $tokens->getNextNonWhitespace($openBodyIndex);

            if ($tokens[$endifIndex]->isGivenKind(T_ENDIF)) {
                // keep the endif as the if statement will break without it
                $this->clearRangeKeepComments($tokens, $elseIndex, $openBodyIndex);
            }

            return;
        }

        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $this->clearRangeKeepComments($tokens, $elseIndex, $closeBodyIndex);
    }

    /**
     * @param int    $doIndex
     * @param Tokens $tokens
     */
    private function fixDoWhile($doIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($doIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $whileIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
        $openBraceIndex = $tokens->getNextMeaningfulToken($whileIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $doIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $doIndex, $closeBraceIndex);
    }

    /**
     * @param int    $forIndex
     * @param Tokens $tokens
     */
    private function fixFor($forIndex, Tokens $tokens)
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($forIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $openBody = $tokens[$openBodyIndex];

        if ($openBody->isGivenKind(T_CLOSE_TAG)) {
            $this->clearRangeKeepComments($tokens, $forIndex, $closeBraceIndex);

            return;
        }

        if ($openBody->equals(';')) {
            $this->clearRangeKeepComments($tokens, $forIndex, $openBodyIndex);

            return;
        }

        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $this->clearRangeKeepComments($tokens, $forIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDFOR)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $forIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $forIndex, $closeBodyIndex);
    }

    /**
     * @param int    $finallyIndex
     * @param Tokens $tokens
     */
    private function fixFinally($finallyIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($finallyIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $this->clearRangeKeepComments($tokens, $finallyIndex, $closeBodyIndex);
    }

    /**
     * @param int    $ifIndex
     * @param Tokens $tokens
     */
    private function fixIf($ifIndex, Tokens $tokens)
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($ifIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        while (true) {
            if (null === $closeBodyIndex) {
                return;
            }

            if ($tokens[$closeBodyIndex]->equals('}')) {
                $nextIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

                if (null === $nextIndex) {
                    $this->clearRangeKeepComments($tokens, $ifIndex, $closeBodyIndex);

                    return;
                }

                $closeBodyIndex = $nextIndex;
            }

            if (null === $closeBodyIndex) {
                return;
            }

            if ($tokens[$closeBodyIndex]->isGivenKind(T_ELSE)) {
                // if `else` still exists, it means that it has a body, as
                // `fixElse` is run before this
                return;
            }

            if ($tokens[$closeBodyIndex]->isGivenKind(T_ENDIF)) {
                $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

                if ($tokens[$semicolonIndex]->equals(';')) {
                    $this->clearRangeKeepComments($tokens, $ifIndex, $semicolonIndex);
                } else {
                    $this->clearRangeKeepComments($tokens, $ifIndex, $closeBodyIndex);
                }

                return;
            }

            if (!$tokens[$closeBodyIndex]->isGivenKind(T_ELSEIF)) {
                return;
            }

            $openElseifBraceIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
            $closeElseifBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openElseifBraceIndex);

            $openElseifBodyIndex = $tokens->getNextMeaningfulToken($closeElseifBraceIndex);
            $closeBodyIndex = $tokens->getNextNonWhitespace($openElseifBodyIndex);
        }
    }

    /**
     * @param int    $switchIndex
     * @param Tokens $tokens
     */
    private function fixSwitch($switchIndex, Tokens $tokens)
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($switchIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $this->clearRangeKeepComments($tokens, $switchIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDSWITCH)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $switchIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $switchIndex, $closeBodyIndex);
    }

    /**
     * @param int    $tryIndex
     * @param Tokens $tokens
     */
    private function fixTry($tryIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($tryIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $clearRangeIndexEnd = $closeBodyIndex;

        $catchOrFinallyIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        while (null !== $catchOrFinallyIndex && $tokens[$catchOrFinallyIndex]->isGivenKind(T_CATCH)) {
            $openCatchBraceIndex = $tokens->getNextMeaningfulToken($catchOrFinallyIndex);
            $closeCatchBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openCatchBraceIndex);
            $openCatchBodyIndex = $tokens->getNextMeaningfulToken($closeCatchBraceIndex);
            $closeCatchBodyIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openCatchBodyIndex);

            $clearRangeIndexEnd = $closeCatchBodyIndex;
            $catchOrFinallyIndex = $tokens->getNextMeaningfulToken($closeCatchBodyIndex);
        }

        if (null !== $catchOrFinallyIndex && $tokens[$catchOrFinallyIndex]->isGivenKind(T_FINALLY)) {
            $openFinallyBodyIndex = $tokens->getNextMeaningfulToken($catchOrFinallyIndex);
            $closeFinallyBodyIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openFinallyBodyIndex);

            $clearRangeIndexEnd = $closeFinallyBodyIndex;
        }

        $this->clearRangeKeepComments($tokens, $tryIndex, $clearRangeIndexEnd);
    }

    /**
     * @param int    $whileIndex
     * @param Tokens $tokens
     */
    private function fixWhile($whileIndex, Tokens $tokens)
    {
        // make sure it's not part of a do-while statement
        $closeDoBodyIndex = $tokens->getPrevMeaningfulToken($whileIndex);

        if ($tokens[$closeDoBodyIndex]->equals('}')) {
            $openDoBodyIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $closeDoBodyIndex);
            $doIndex = $tokens->getPrevMeaningfulToken($openDoBodyIndex);

            if ($tokens[$doIndex]->isGivenKind(T_DO)) {
                return;
            }
        }

        $openBraceIndex = $tokens->getNextMeaningfulToken($whileIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $openBody = $tokens[$openBodyIndex];

        if ($openBody->isGivenKind(T_CLOSE_TAG)) {
            $this->clearRangeKeepComments($tokens, $whileIndex, $closeBraceIndex);

            return;
        }

        if ($openBody->equals(';')) {
            $this->clearRangeKeepComments($tokens, $whileIndex, $openBodyIndex);

            return;
        }

        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $this->clearRangeKeepComments($tokens, $whileIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDWHILE)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $whileIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $whileIndex, $closeBodyIndex);
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return bool
     */
    private function canHaveSideEffects(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($index = $endIndex; $startIndex <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind([
                // loading files
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                // __get with side effects
                T_OBJECT_OPERATOR,
                // modification
                T_INC,
                T_DEC,
                T_CONCAT_EQUAL,
                T_DIV_EQUAL,
                T_MINUS_EQUAL,
                T_MOD_EQUAL,
                T_MUL_EQUAL,
                T_PLUS_EQUAL,
                T_POW_EQUAL,
                T_AND_EQUAL,
                T_OR_EQUAL,
                T_SL_EQUAL,
                T_SR_EQUAL,
                T_XOR_EQUAL,
            ])) {
                return true;
            }

            if ($token->equalsAny([
                // function calls
                '(',
                // offsetGet with side effects
                '[',
                // modification
                '=',
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function clearRangeKeepComments(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($index = $endIndex; $startIndex <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }
        }
    }
}
