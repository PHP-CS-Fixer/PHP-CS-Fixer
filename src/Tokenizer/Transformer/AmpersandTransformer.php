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
 * Transforms ampersand T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG and T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG into single CT::T_AMPERSAND.
 *
 * @internal
 */
final class AmpersandTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 80100;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if (!$tokens[$index]->isGivenKind([T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG])) {
            return;
        }

        $tokens[$index] = new Token([CT::T_AMPERSAND, '&']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [
            CT::T_AMPERSAND,
        ];
    }
}
