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
 * Transform `&` operator into CT::T_RETURN_REF in `function & foo() {}`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ReturnRefTransformer extends AbstractTransformer
{
    public function getRequiredPhpVersionId(): int
    {
        return 5_00_00;
    }

    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if ($token->equals('&') && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind([T_FUNCTION, T_FN])) {
            $tokens[$index] = new Token([CT::T_RETURN_REF, '&']);
        }
    }

    public function getCustomTokens(): array
    {
        return [CT::T_RETURN_REF];
    }
}
