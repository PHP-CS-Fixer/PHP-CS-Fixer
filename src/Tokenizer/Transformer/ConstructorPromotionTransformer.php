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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transforms for Constructor Property Promotion.
 *
 * Transform T_PUBLIC, T_PROTECTED and T_PRIVATE of Constructor Property Promotion into custom tokens.
 *
 * @internal
 */
final class ConstructorPromotionTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 80000;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
            return;
        }

        $index = $tokens->getNextMeaningfulToken($index);

        if (!$tokens[$index]->isGivenKind(T_STRING) || '__construct' !== strtolower($tokens[$index]->getContent())) {
            return;
        }

        /** @var int $openIndex */
        $openIndex = $tokens->getNextMeaningfulToken($index); // we are @ '(' now
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        for ($index = $openIndex; $index < $closeIndex; ++$index) {
            if ($tokens[$index]->isGivenKind(T_PUBLIC)) {
                $tokens[$index] = new Token([CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, $tokens[$index]->getContent()]);
            } elseif ($tokens[$index]->isGivenKind(T_PROTECTED)) {
                $tokens[$index] = new Token([CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, $tokens[$index]->getContent()]);
            } elseif ($tokens[$index]->isGivenKind(T_PRIVATE)) {
                $tokens[$index] = new Token([CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE, $tokens[$index]->getContent()]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDeprecatedCustomTokens()
    {
        return [
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
        ];
    }
}
