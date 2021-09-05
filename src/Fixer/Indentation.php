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

namespace PhpCsFixer\Fixer;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
trait Indentation
{
    private function extractIndent(string $content): string
    {
        if (Preg::match('/\R(\h*)[^\r\n]*$/D', $content, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function computeNewLineContent(Tokens $tokens, int $index): string
    {
        $content = $tokens[$index]->getContent();

        if (0 !== $index && $tokens[$index - 1]->equalsAny([[T_OPEN_TAG], [T_CLOSE_TAG]])) {
            $content = Preg::replace('/\S/', '', $tokens[$index - 1]->getContent()).$content;
        }

        return $content;
    }

    private function isNewLineToken(Tokens $tokens, int $index): bool
    {
        $token = $tokens[$index];

        if (
            $token->isGivenKind(T_OPEN_TAG)
            && isset($tokens[$index + 1])
            && !$tokens[$index + 1]->isWhitespace()
            && Preg::match('/\R/', $token->getContent())
        ) {
            return true;
        }

        if (!$tokens[$index]->isGivenKind([T_WHITESPACE, T_INLINE_HTML])) {
            return false;
        }

        return (bool) Preg::match('/\R/', $this->computeNewLineContent($tokens, $index));
    }
}
