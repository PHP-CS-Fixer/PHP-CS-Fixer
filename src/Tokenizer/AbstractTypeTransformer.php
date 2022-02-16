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

namespace PhpCsFixer\Tokenizer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractTypeTransformer extends AbstractTransformer
{
    protected function doProcess(Tokens $tokens, int $index, $originalToken): void
    {
        if (!$tokens[$index]->equals($originalToken)) {
            return;
        }

        $prevIndex = $tokens->getTokenNotOfKindsSibling($index, -1, [T_CALLABLE, T_NS_SEPARATOR, T_STRING, CT::T_ARRAY_TYPEHINT, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT]);

        /** @var Token $prevToken */
        $prevToken = $tokens[$prevIndex];

        if ($prevToken->isGivenKind([
            CT::T_TYPE_COLON, // `:` is part of a function return type `foo(): X|Y`
            CT::T_TYPE_ALTERNATION, // `|` is part of a union (chain) `X|Y`
            CT::T_TYPE_INTERSECTION,
            T_STATIC, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, // `var X|Y $a;`, `private X|Y $a` or `public static X|Y $a`
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, // promoted properties
        ])) {
            $this->replaceToken($tokens, $index);

            return;
        }

        if (\defined('T_READONLY') && $prevToken->isGivenKind(T_READONLY)) { // @TODO: drop condition when PHP 8.1+ is required
            $this->replaceToken($tokens, $index);

            return;
        }

        if (!$prevToken->equalsAny(['(', ','])) {
            return;
        }

        $prevPrevTokenIndex = $tokens->getPrevMeaningfulToken($prevIndex);

        if ($tokens[$prevPrevTokenIndex]->isGivenKind(T_CATCH)) {
            $this->replaceToken($tokens, $index);

            return;
        }

        $functionKinds = [[T_FUNCTION], [T_FN]];
        $functionIndex = $tokens->getPrevTokenOfKind($prevIndex, $functionKinds);

        if (null === $functionIndex) {
            return;
        }

        $braceOpenIndex = $tokens->getNextTokenOfKind($functionIndex, ['(']);
        $braceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $braceOpenIndex);

        if ($braceCloseIndex < $index) {
            return;
        }

        $this->replaceToken($tokens, $index);
    }

    abstract protected function replaceToken(Tokens $tokens, int $index): void;
}
