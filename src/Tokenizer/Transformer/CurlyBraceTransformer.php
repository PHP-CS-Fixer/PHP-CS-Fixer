<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform discriminate overloaded curly braces tokens.
 *
 * Performed transformations:
 * - closing `}` for T_CURLY_OPEN into CT::T_CURLY_CLOSE,
 * - closing `}` for T_DOLLAR_OPEN_CURLY_BRACES into CT::T_DOLLAR_CLOSE_CURLY_BRACES,
 * - in `$foo->{$bar}` into CT::T_DYNAMIC_PROP_BRACE_OPEN and CT::T_DYNAMIC_PROP_BRACE_CLOSE,
 * - in `${$foo}` into CT::T_DYNAMIC_VAR_BRACE_OPEN and CT::T_DYNAMIC_VAR_BRACE_CLOSE,
 * - in `$array{$index}` into CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN and CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
 * - in `use some\a\{ClassA, ClassB, ClassC as C}` into CT::T_GROUP_IMPORT_BRACE_OPEN, CT::T_GROUP_IMPORT_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CurlyBraceTransformer extends AbstractTransformer
{
    public function getRequiredPhpVersionId(): int
    {
        return 5_00_00;
    }

    public function process(Tokens $tokens, Token $token, int $index): void
    {
        $this->transformIntoCurlyCloseBrace($tokens, $token, $index);
        $this->transformIntoDollarCloseBrace($tokens, $token, $index);
        $this->transformIntoDynamicPropBraces($tokens, $token, $index);
        $this->transformIntoDynamicVarBraces($tokens, $token, $index);
        $this->transformIntoCurlyIndexBraces($tokens, $token, $index);
        $this->transformIntoGroupUseBraces($tokens, $token, $index);
    }

    public function getCustomTokens(): array
    {
        return [
            CT::T_CURLY_CLOSE,
            CT::T_DOLLAR_CLOSE_CURLY_BRACES,
            CT::T_DYNAMIC_PROP_BRACE_OPEN,
            CT::T_DYNAMIC_PROP_BRACE_CLOSE,
            CT::T_DYNAMIC_VAR_BRACE_OPEN,
            CT::T_DYNAMIC_VAR_BRACE_CLOSE,
            CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
            CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            CT::T_GROUP_IMPORT_BRACE_OPEN,
            CT::T_GROUP_IMPORT_BRACE_CLOSE,
        ];
    }

    /**
     * Transform closing `}` for T_CURLY_OPEN into CT::T_CURLY_CLOSE.
     *
     * This should be done at very beginning of curly braces transformations.
     */
    private function transformIntoCurlyCloseBrace(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->isGivenKind(T_CURLY_OPEN)) {
            return;
        }

        $level = 1;

        do {
            ++$index;

            if ($tokens[$index]->equals('{') || $tokens[$index]->isGivenKind(T_CURLY_OPEN)) { // we count all kind of {
                ++$level;
            } elseif ($tokens[$index]->equals('}')) { // we count all kind of }
                --$level;
            }
        } while (0 < $level);

        $tokens[$index] = new Token([CT::T_CURLY_CLOSE, '}']);
    }

    private function transformIntoDollarCloseBrace(Tokens $tokens, Token $token, int $index): void
    {
        if ($token->isGivenKind(T_DOLLAR_OPEN_CURLY_BRACES)) {
            $nextIndex = $tokens->getNextTokenOfKind($index, ['}']);
            $tokens[$nextIndex] = new Token([CT::T_DOLLAR_CLOSE_CURLY_BRACES, '}']);
        }
    }

    private function transformIntoDynamicPropBraces(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->isObjectOperator()) {
            return;
        }

        if (!$tokens[$index + 1]->equals('{')) {
            return;
        }

        $openIndex = $index + 1;
        $closeIndex = $this->naivelyFindCurlyBlockEnd($tokens, $openIndex);

        $tokens[$openIndex] = new Token([CT::T_DYNAMIC_PROP_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new Token([CT::T_DYNAMIC_PROP_BRACE_CLOSE, '}']);
    }

    private function transformIntoDynamicVarBraces(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->equals('$')) {
            return;
        }

        $openIndex = $tokens->getNextMeaningfulToken($index);

        if (null === $openIndex) {
            return;
        }

        $openToken = $tokens[$openIndex];

        if (!$openToken->equals('{')) {
            return;
        }

        $closeIndex = $this->naivelyFindCurlyBlockEnd($tokens, $openIndex);

        $tokens[$openIndex] = new Token([CT::T_DYNAMIC_VAR_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new Token([CT::T_DYNAMIC_VAR_BRACE_CLOSE, '}']);
    }

    private function transformIntoCurlyIndexBraces(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->equals('{')) {
            return;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$prevIndex]->equalsAny([
            [T_STRING],
            [T_VARIABLE],
            [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE],
            ']',
            ')',
        ])) {
            return;
        }

        if (
            $tokens[$prevIndex]->isGivenKind(T_STRING)
            && !$tokens[$tokens->getPrevMeaningfulToken($prevIndex)]->isObjectOperator()
        ) {
            return;
        }

        if (
            $tokens[$prevIndex]->equals(')')
            && !$tokens[$tokens->getPrevMeaningfulToken(
                $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $prevIndex)
            )]->isGivenKind(T_ARRAY)
        ) {
            return;
        }

        $closeIndex = $this->naivelyFindCurlyBlockEnd($tokens, $index);

        $tokens[$index] = new Token([CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new Token([CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE, '}']);
    }

    private function transformIntoGroupUseBraces(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->equals('{')) {
            return;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
            return;
        }

        $closeIndex = $this->naivelyFindCurlyBlockEnd($tokens, $index);

        $tokens[$index] = new Token([CT::T_GROUP_IMPORT_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new Token([CT::T_GROUP_IMPORT_BRACE_CLOSE, '}']);
    }

    /**
     * We do not want to rely on `$tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index)` here,
     * as it relies on block types that are assuming that `}` tokens are already transformed to Custom Tokens that are allowing to distinguish different block types.
     * As we are just about to transform `{` and `}` into Custom Tokens by this transformer, thus we need to compare those tokens manually by content without using `Tokens::findBlockEnd`.
     */
    private function naivelyFindCurlyBlockEnd(Tokens $tokens, int $startIndex): int
    {
        if (!$tokens->offsetExists($startIndex)) {
            throw new \OutOfBoundsException(sprintf('Unavailable index: "%s".', $startIndex));
        }

        if ('{' !== $tokens[$startIndex]->getContent()) {
            throw new \InvalidArgumentException(sprintf('Wrong start index: "%s".', $startIndex));
        }

        $blockLevel = 1;
        $endIndex = $tokens->count() - 1;
        for ($index = $startIndex + 1; $index !== $endIndex; ++$index) {
            $token = $tokens[$index];

            if ('{' === $token->getContent()) {
                ++$blockLevel;

                continue;
            }

            if ('}' === $token->getContent()) {
                --$blockLevel;

                if (0 === $blockLevel) {
                    if (!$token->equals('}')) {
                        throw new \UnexpectedValueException(sprintf('Detected block end for index: "%s" was already transformed into other token type: "%s".', $startIndex, $token->getName()));
                    }

                    return $index;
                }
            }
        }

        throw new \UnexpectedValueException(sprintf('Missing block end for index: "%s".', $startIndex));
    }
}
