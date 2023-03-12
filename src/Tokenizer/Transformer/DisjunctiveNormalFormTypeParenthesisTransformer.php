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
 * Transform DNF parentheses into CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN and CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE.
 *
 * @see https://wiki.php.net/rfc/dnf_types
 *
 * @internal
 */
final class DisjunctiveNormalFormTypeParenthesisTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        // needs to run after TypeAlternationTransformer
        return -16;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 8_02_00;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if ($token->equals('(') && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(CT::T_TYPE_ALTERNATION)) {
            $openIndex = $index;
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
        } elseif ($token->equals(')') && $tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(CT::T_TYPE_ALTERNATION)) {
            $openIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $closeIndex = $index;
        } else {
            return;
        }

        $tokens[$openIndex] = new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, '(']);
        $tokens[$closeIndex] = new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE, ')']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [
            CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
            CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
        ];
    }
}
