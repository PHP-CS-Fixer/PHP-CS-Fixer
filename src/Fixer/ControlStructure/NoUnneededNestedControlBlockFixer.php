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
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoUnneededNestedControlBlockFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must not be unneeded nested `if-if` and `else-if` blocks.',
            [
                new CodeSample(
                    '<?php
if ($a) {
    if ($b) {
    }
}
'
                ),
                new CodeSample(
                    '<?php
if ($a) {

} else {
    if ($b) {
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoSuperfluousElseifFixer, NoUselessElseFixer.
     * Must run after NoEmptyStatementFixer, NoUnneededCurlyBracesFixer.
     */
    public function getPriority()
    {
        return 26;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_IF);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokenCount = \count($tokens) - 3;

        for ($index = $tokenCount; $index > 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind([T_IF, T_ELSEIF])) {
                $this->applyFixIf($tokens, $index);
            } elseif ($token->isGivenKind(T_ELSE)) {
                $this->applyFixElse($tokens, $index);
            }
        }
    }

    /**
     * @param int $ifTokenIndex of T_IF or T_ELSEIF
     */
    private function applyFixIf(Tokens $tokens, $ifTokenIndex)
    {
        $ifOpenBraceIndex = $tokens->getNextMeaningfulToken($ifTokenIndex);
        $ifCloseBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $ifOpenBraceIndex);

        $ifBlockOpenIndex = $tokens->getNextMeaningfulToken($ifCloseBraceIndex);
        $block = $this->getNestedBlockInfo($tokens, $ifBlockOpenIndex);

        if (null === $block) {
            return;
        }

        $afterParentBlockClose = $tokens->getNextMeaningfulToken($block['parent']['block_close']);

        if (null !== $afterParentBlockClose && $tokens[$afterParentBlockClose]->isGivenKind([T_ELSE, T_ELSEIF])) {
            return;
        }

        // merge the conditions from the parent and nested block into a new one

        $newConditionTokens = $this->generateJoinedConditions(
            $tokens,
            $ifOpenBraceIndex,
            $ifCloseBraceIndex,
            $block['nested']['brace_open'],
            $block['nested']['brace_close']
        );

        // remove the no longer needed nested block

        $this->removeNestedBlock($tokens, $block['nested']);

        // remove the parent condition statement

        for ($i = $ifCloseBraceIndex - 1; $i > $ifOpenBraceIndex; --$i) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
        }

        // insert the new combined condition statement at the parents location

        $tokens->insertAt($ifOpenBraceIndex + 1, $newConditionTokens);
    }

    /**
     * @param int $elseTokenIndex of T_ELSE
     */
    private function applyFixElse(Tokens $tokens, $elseTokenIndex)
    {
        $elseBlockOpenIndex = $tokens->getNextMeaningfulToken($elseTokenIndex);
        $block = $this->getNestedBlockInfo($tokens, $elseBlockOpenIndex);

        if (null === $block) {
            return;
        }

        // clone the nested if condition for insert later

        $newCondition = [];
        $newCondition[] = new Token([T_WHITESPACE, ' ']);

        for ($i = $block['nested']['brace_open']; $i <= $block['nested']['brace_close']; ++$i) {
            if (!$tokens->isEmptyAt($i)) {
                $newCondition[] = clone $tokens[$i];
            }
        }

        // remove the no longer needed nested block

        $this->removeNestedBlock($tokens, $block['nested']);

        // insert the new tokens

        $tokens[$elseTokenIndex] = new Token([T_ELSEIF, 'elseif']);
        $tokens->insertAt($elseTokenIndex + 1, $newCondition);
    }

    /**
     * @param int $openBraceIndex1
     * @param int $closeBraceIndex1
     * @param int $openBraceIndex2
     * @param int $closeBraceIndex2
     *
     * @return Token[]
     */
    private function generateJoinedConditions(
        Tokens $tokens,
        $openBraceIndex1,
        $closeBraceIndex1,
        $openBraceIndex2,
        $closeBraceIndex2
    ) {
        $newCondition = [];

        // check if the first condition needs to be wrapped in parenthesis

        $requiresBracesBlock1 = $this->hasLowerPrecedence($tokens, $openBraceIndex1 + 1, $closeBraceIndex1 - 1);

        if ($requiresBracesBlock1) {
            $newCondition[] = new Token('(');
        }

        for ($i = $openBraceIndex1 + 1; $i < $closeBraceIndex1; ++$i) {
            if (!$tokens->isEmptyAt($i)) {
                $newCondition[] = clone $tokens[$i];
            }
        }

        if ($requiresBracesBlock1) {
            $newCondition[] = new Token(')');
        }

        $newCondition[] = new Token([T_WHITESPACE, ' ']);
        $newCondition[] = new Token([T_BOOLEAN_AND, '&&']);
        $newCondition[] = new Token([T_WHITESPACE, ' ']);

        // check if the second condition needs to be wrapped in parenthesis

        $requiresBracesBlock2 = $this->hasLowerPrecedence($tokens, $openBraceIndex2 + 1, $closeBraceIndex2 - 1);

        if (!$requiresBracesBlock2) {
            ++$openBraceIndex2;
            --$closeBraceIndex2;
        }

        for ($i = $openBraceIndex2; $i <= $closeBraceIndex2; ++$i) {
            if (!$tokens->isEmptyAt($i)) {
                $newCondition[] = clone $tokens[$i];
            }
        }

        return $newCondition;
    }

    /**
     * @param int $parentBlockOpenIndex
     *
     * @return null|array
     */
    private function getNestedBlockInfo(Tokens $tokens, $parentBlockOpenIndex)
    {
        if (!$tokens[$parentBlockOpenIndex]->equals('{')) {
            return null;
        }

        $ifTokenIndex = $tokens->getNextMeaningfulToken($parentBlockOpenIndex);

        // if next meaningful token is not T_IF than it is not a case for fixing

        if (!$tokens[$ifTokenIndex]->isGivenKind(T_IF)) {
            return null;
        }

        // if next meaningful token is T_IF, but uses an alternative syntax than it is not a case for fixing

        $conditionOpenBraceIndex = $tokens->getNextMeaningfulToken($ifTokenIndex);
        $conditionEndBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $conditionOpenBraceIndex);
        $afterConditionOpenIndex = $tokens->getNextMeaningfulToken($conditionEndBraceIndex);

        if (!$tokens[$afterConditionOpenIndex]->equals('{')) { // ':'
            return null;
        }

        $afterConditionEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $afterConditionOpenIndex);
        $parentBlockCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $parentBlockOpenIndex);

        // if there is something meaningful in the parent block other than the nested block than it is not a case for fixing

        if ($tokens->getNextMeaningfulToken($afterConditionEndIndex) !== $parentBlockCloseIndex) {
            return null;
        }

        return [
            'parent' => [
                'block_open' => $parentBlockOpenIndex,
                'block_close' => $parentBlockCloseIndex,
            ],
            'nested' => [
                'if' => $ifTokenIndex,
                'brace_open' => $conditionOpenBraceIndex,
                'brace_close' => $conditionEndBraceIndex,
                'block_open' => $afterConditionOpenIndex,
                'block_close' => $afterConditionEndIndex,
            ],
        ];
    }

    /**
     * Returns if contains operation with lower precedence than '&&'.
     *
     * @param int $startIndex
     * @param int $endIndex
     *
     * @return bool
     */
    private function hasLowerPrecedence(Tokens $tokens, $startIndex, $endIndex)
    {
        $lowerPrecedenceKindsKind = [
            T_LOGICAL_AND,  // and
            T_LOGICAL_OR,   // or
            T_LOGICAL_XOR,  // xor
            T_YIELD,        // yield
            T_BOOLEAN_OR,   // ||
            T_PLUS_EQUAL,   // +=
            T_MINUS_EQUAL,  // -=
            T_MUL_EQUAL,    // *=
            T_DIV_EQUAL,    // /=
            T_CONCAT_EQUAL, // .=
            T_MOD_EQUAL,    // %=
            T_AND_EQUAL,    // &=
            T_OR_EQUAL,     // |=
            T_XOR_EQUAL,    // ^=
            T_SR_EQUAL,     // >>=
            T_SL_EQUAL,     // <<=
            T_POW_EQUAL,    // **=
        ];

        if (\defined('T_YIELD_FROM')) {
            $lowerPrecedenceKindsKind[] = T_YIELD_FROM;
        }

        if (\defined('T_COALESCE')) {
            $lowerPrecedenceKindsKind[] = T_COALESCE;
        }

        if (\defined('T_COALESCE_EQUAL')) {
            $lowerPrecedenceKindsKind[] = T_COALESCE_EQUAL;
        }

        $startIndex = $tokens->getNextMeaningfulToken($startIndex);

        for (; $startIndex <= $endIndex; ++$startIndex) {
            $blockType = Tokens::detectBlockType($tokens[$startIndex]);

            while (null !== $blockType && $blockType['isStart']) {
                $startIndex = $tokens->findBlockEnd($blockType['type'], $startIndex);
                $startIndex = $tokens->getNextMeaningfulToken($startIndex);
                $blockType = Tokens::detectBlockType($tokens[$startIndex]);
            }

            if ($tokens[$startIndex]->equalsAny(['=', '?']) || $tokens[$startIndex]->isGivenKind($lowerPrecedenceKindsKind)) {
                return true;
            }
        }

        return false;
    }

    private function removeNestedBlock(Tokens $tokens, array $block)
    {
        // remove the nested braces

        $this->removeTrailingSpaceBefore($tokens, $block['block_close']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($block['block_close']);

        $this->removeTrailingSpaceBefore($tokens, $block['block_open']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($block['block_open']);

        // remove the nested condition statement

        for ($i = $block['brace_open']; $i < $block['brace_close']; ++$i) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
            $this->removeTrailingSpaceBefore($tokens, $i);
        }

        $this->removeTrailingSpaceBefore($tokens, $block['brace_close']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($block['brace_close']);

        // remove the nested `if`

        $this->removeTrailingSpaceBefore($tokens, $block['if']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($block['if']);
    }

    /**
     * @param int $index
     */
    private function removeTrailingSpaceBefore(Tokens $tokens, $index)
    {
        $beforeBlockCloseIndex = $tokens->getNonEmptySibling($index, -1);

        if (!$tokens[$beforeBlockCloseIndex]->isWhitespace()) {
            return;
        }

        $lines = Preg::split('/(\\R+)/', $tokens[$beforeBlockCloseIndex]->getContent(), -1, PREG_SPLIT_DELIM_CAPTURE);
        $linesSize = \count($lines);

        if (0 === $linesSize) {
            return;
        }

        if (1 === $linesSize) {
            $tokens->clearAt($beforeBlockCloseIndex);
        } else {
            unset($lines[$linesSize - 1]);
            $newContent = implode('', $lines);
            $tokens[$beforeBlockCloseIndex] = new Token([T_WHITESPACE, $newContent]);
        }
    }
}
