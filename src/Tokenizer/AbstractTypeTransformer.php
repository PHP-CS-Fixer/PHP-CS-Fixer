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
    /**
     * @param array{0: int, 1: string}|string $originalToken
     */
    protected function doProcess(Tokens $tokens, int $candidateIndex, $originalToken): void
    {
        if (!$tokens[$candidateIndex]->equals($originalToken)) {
            return;
        }

        $index = $candidateIndex;
        while ($index > 0) {
            --$index;

            if ($tokens[$index]->equalsAny([';', '{', '}', '='])) {
                return;
            }

            $blockType = Tokens::detectBlockType($tokens[$index]);
            if (null !== $blockType && !$blockType['isStart']) {
                $index = $tokens->findBlockStart($blockType['type'], $index);

                continue;
            }

            if ($tokens[$index]->isGivenKind([
                CT::T_TYPE_COLON, // `:` is part of a function return type `foo(): X|Y`
                CT::T_TYPE_ALTERNATION, // `|` is part of a union (chain) `X|Y`
                CT::T_TYPE_INTERSECTION,
                T_STATIC, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, // `var X|Y $a;`, `private X|Y $a` or `public static X|Y $a`
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, // promoted properties
                T_FN, T_FUNCTION,
                T_CATCH,
            ])) {
                $this->replaceToken($tokens, $candidateIndex);

                return;
            }
        }
    }

    abstract protected function replaceToken(Tokens $tokens, int $index): void;
}
