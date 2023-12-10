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
 * Transform T_USE into:
 * - CT::T_USE_TRAIT for imports,
 * - CT::T_USE_LAMBDA for lambda variable uses.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class UseTransformer extends AbstractTransformer
{
    public function getPriority(): int
    {
        // Should run after CurlyBraceTransformer and before TypeColonTransformer
        return -5;
    }

    public function getRequiredPhpVersionId(): int
    {
        return 5_03_00;
    }

    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if ($token->isGivenKind(T_USE) && $this->isUseForLambda($tokens, $index)) {
            $tokens[$index] = new Token([CT::T_USE_LAMBDA, $token->getContent()]);

            return;
        }

        // Only search inside class/trait body for `T_USE` for traits.
        // Cannot import traits inside interfaces or anywhere else

        $classTypes = [T_TRAIT];

        if (\defined('T_ENUM')) { // @TODO: drop condition when PHP 8.1+ is required
            $classTypes[] = T_ENUM;
        }

        if ($token->isGivenKind(T_CLASS)) {
            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_DOUBLE_COLON)) {
                return;
            }
        } elseif (!$token->isGivenKind($classTypes)) {
            return;
        }

        $index = $tokens->getNextTokenOfKind($index, ['{']);
        $innerLimit = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

        while ($index < $innerLimit) {
            $token = $tokens[++$index];

            if (!$token->isGivenKind(T_USE)) {
                continue;
            }

            if ($this->isUseForLambda($tokens, $index)) {
                $tokens[$index] = new Token([CT::T_USE_LAMBDA, $token->getContent()]);
            } else {
                $tokens[$index] = new Token([CT::T_USE_TRAIT, $token->getContent()]);
            }
        }
    }

    public function getCustomTokens(): array
    {
        return [CT::T_USE_TRAIT, CT::T_USE_LAMBDA];
    }

    /**
     * Check if token under given index is `use` statement for lambda function.
     */
    private function isUseForLambda(Tokens $tokens, int $index): bool
    {
        $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        // test `function () use ($foo) {}` case
        return $nextToken->equals('(');
    }
}
