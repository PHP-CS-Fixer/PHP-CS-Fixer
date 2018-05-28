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
 *
 * @todo for
 * @todo correct if/elseif/else
 * @todo correct alternate syntax
 */
final class NoEmptyBlockFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must not be any empty blocks.',
            [
                new CodeSample('<?php if ($foo) {}'),
                new CodeSample('<?php switch ($foo) {}'),
                new CodeSample('<?php while ($foo) {}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([
            T_FINALLY,
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

            if ($token->isGivenKind(T_DO)) {
                $this->fixDoWhile($index, $tokens);
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
     * @param int    $doIndex
     * @param Tokens $tokens
     */
    private function fixDoWhile($doIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($doIndex);
        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $whileIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
        $openBraceIndex = $tokens->getNextMeaningfulToken($whileIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $tokens->clearRange($doIndex, $tokens->getNextMeaningfulToken($closeBraceIndex));
    }

    /**
     * @param int    $finallyIndex
     * @param Tokens $tokens
     */
    private function fixFinally($finallyIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($finallyIndex);
        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $tokens->clearRange($finallyIndex, $closeBodyIndex);
    }

    /**
     * @param int    $ifIndex
     * @param Tokens $tokens
     */
    private function fixIf($ifIndex, Tokens $tokens)
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($ifIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        if ($this->canHaveSideEffects($tokens, $openBraceIndex + 1, $closeBraceIndex - 1)) {
            return;
        }

        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $possibleElseOrElseifIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if (null !== $possibleElseOrElseifIndex && $tokens[$possibleElseOrElseifIndex]->isGivenKind([T_ELSE, T_ELSEIF])) {
            return;
        }

        $tokens->clearRange($ifIndex, $closeBodyIndex);
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
        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $tokens->clearRange($switchIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDSWITCH)) {
            return;
        }

        // endswitch must have a semicolon after
        $tokens->clearRange($switchIndex, $tokens->getNextMeaningfulToken($closeBodyIndex));
    }

    /**
     * @param int    $tryIndex
     * @param Tokens $tokens
     */
    private function fixTry($tryIndex, Tokens $tokens)
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($tryIndex);
        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

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

        $tokens->clearRange($tryIndex, $clearRangeIndexEnd);
    }

    /**
     * @param int    $whileIndex
     * @param Tokens $tokens
     */
    private function fixWhile($whileIndex, Tokens $tokens)
    {
        // make sure it's not part of a do-while statement, which is dealt with
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

        if ($tokens[$openBodyIndex]->equals(';')) {
            $tokens->clearRange($whileIndex, $openBodyIndex);

            return;
        }

        $closeBodyIndex = $tokens->getNextMeaningfulToken($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $tokens->clearRange($whileIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDWHILE)) {
            return;
        }

        // endwhile must have a semicolon after
        $tokens->clearRange($whileIndex, $tokens->getNextMeaningfulToken($closeBodyIndex));
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
}
