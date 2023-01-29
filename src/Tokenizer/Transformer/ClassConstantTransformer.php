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
 * Transform `class` class' constant from T_CLASS into CT::T_CLASS_CONSTANT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ClassConstantTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 5_05_00;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if (!$token->equalsAny([
            [T_CLASS, 'class'],
            [T_STRING, 'class'],
        ], false)) {
            return;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        $prevToken = $tokens[$prevIndex];

        if ($prevToken->isGivenKind(T_DOUBLE_COLON)) {
            $tokens[$index] = new Token([CT::T_CLASS_CONSTANT, $token->getContent()]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [CT::T_CLASS_CONSTANT];
    }
}
