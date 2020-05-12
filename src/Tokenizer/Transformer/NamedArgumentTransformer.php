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
 * Transform named argument tokens.
 *
 * @internal
 */
final class NamedArgumentTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        // needs to run after TypeColonTransformer
        return -15;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 80000;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if (!$tokens[$index]->equals(':')) {
            return;
        }

        $stringIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$stringIndex]->isGivenKind(T_STRING)) {
            return;
        }

        $preStringIndex = $tokens->getPrevMeaningfulToken($stringIndex);

        // if equals any [';', '{', '}', [T_OPEN_TAG]] than it is a goto label
        // if equals ')' than likely it is a type colon, but sure not a name argument
        // if equals '?' than it is part of ternary statement

        if (!$tokens[$preStringIndex]->equalsAny([',', '('])) {
            return;
        }

        $tokens[$stringIndex] = new Token([CT::T_NAMED_ARGUMENT_NAME, $tokens[$stringIndex]->getContent()]);
        $tokens[$index] = new Token([CT::T_NAMED_ARGUMENT_COLON, ':']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [
            CT::T_NAMED_ARGUMENT_COLON,
            CT::T_NAMED_ARGUMENT_NAME,
        ];
    }
}
