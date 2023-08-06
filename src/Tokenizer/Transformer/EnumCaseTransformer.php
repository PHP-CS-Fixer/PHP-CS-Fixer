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
 * Transforms `T_CASE` of enum cases into `CT::T_ENUM_CASE`.
 *
 * @internal
 *
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class EnumCaseTransformer extends AbstractTransformer
{
    public function getRequiredPhpVersionId(): int
    {
        return 8_01_00;
    }

    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if (!\defined('T_ENUM') || !$token->isGivenKind(T_ENUM)) {
            return;
        }

        $braceOpenIndex = $tokens->getNextTokenOfKind($index, ['{']);
        $braceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $braceOpenIndex);

        for ($i = $braceOpenIndex; $i < $braceCloseIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(T_SWITCH)) {
                $switchBraceOpenIndex = $tokens->getNextTokenOfKind($i, ['{']);
                $switchBraceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $switchBraceOpenIndex);

                // skip switch cases
                $i += $switchBraceCloseIndex;

                continue;
            }

            if ($tokens[$i]->isGivenKind(T_CASE)) {
                $tokens[$i] = new Token([CT::T_ENUM_CASE, $tokens[$i]->getContent()]);
            }
        }
    }

    public function getCustomTokens(): array
    {
        return [
            CT::T_ENUM_CASE,
        ];
    }
}
