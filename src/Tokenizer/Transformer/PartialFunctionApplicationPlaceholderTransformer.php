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
 * Transforms the `?` placeholder of a partial function application into CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER.
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PartialFunctionApplicationPlaceholderTransformer extends AbstractTransformer
{
    public function getPriority(): int
    {
        // needs to run before NullableTypeTransformer
        return -10;
    }

    public function getRequiredPhpVersionId(): int
    {
        return 8_06_00;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('?');
    }

    public function process(Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (
                $token->equals('?')
                && $tokens[$tokens->getNextMeaningfulToken($index)]->equalsAny([',', ')'])
            ) {
                $tokens[$index] = new Token([CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER, '?']);
            }
        }
    }

    public function getCustomTokens(): array
    {
        return [
            CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
        ];
    }
}
