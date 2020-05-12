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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 * @TODO 4.0 remove this analyzer and move this logic into a transformer
 */
final class AlternativeSyntaxAnalyzer
{
    public function belongsToAlternativeSyntax(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->equals(':')) {
            return false;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if ($tokens[$prevIndex]->isGivenKind(T_ELSE)) {
            return true;
        }

        if (!$tokens[$prevIndex]->equals(')')) {
            return false;
        }

        $openParenthesisIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $prevIndex);
        $beforeOpenParenthesisIndex = $tokens->getPrevMeaningfulToken($openParenthesisIndex);

        return $tokens[$beforeOpenParenthesisIndex]->isGivenKind([
            T_DECLARE,
            T_ELSEIF,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_SWITCH,
            T_WHILE,
        ]);
    }
}
