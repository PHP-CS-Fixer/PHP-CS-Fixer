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
 * Transform `|` operator into CT::T_TYPE_ALTERNATION in `function foo(Type1 | Type2 $x) {`
 *                                                    or `} catch (ExceptionType1 | ExceptionType2 $e) {`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TypeAlternationTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // needs to run after ArrayTypehintTransformer and TypeColonTransformer
        return -15;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 70100;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$token->equals('|')) {
            return;
        }

        $prevIndex = $tokens->getTokenNotOfKindsSibling($index, -1, [T_NS_SEPARATOR, T_STRING, CT::T_ARRAY_TYPEHINT, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT]);

        /** @var Token $prevToken */
        $prevToken = $tokens[$prevIndex];

        if ($prevToken->isGivenKind([
            CT::T_TYPE_COLON, // `:` is part of a function return type `foo(): X|Y`
            CT::T_TYPE_ALTERNATION, // `|` is part of a union (chain) `X|Y`
            T_STATIC, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, // `var X|Y $a;`, `private X|Y $a` or `public static X|Y $a`
        ])) {
            $this->replaceToken($tokens, $index);

            return;
        }

        if (!$prevToken->equalsAny(['(', ','])) {
            return;
        }

        $prevPrevTokenIndex = $tokens->getPrevMeaningfulToken($prevIndex);

        if ($tokens[$prevPrevTokenIndex]->isGivenKind([T_CATCH])) {
            $this->replaceToken($tokens, $index);

            return;
        }

        $functionKinds = [[T_FUNCTION]];

        if (\defined('T_FN')) {
            $functionKinds[] = [T_FN];
        }

        $functionIndex = $tokens->getPrevTokenOfKind($prevIndex, $functionKinds);

        if (null === $functionIndex) {
            return;
        }

        $braceOpenIndex = $tokens->getNextTokenOfKind($functionIndex, ['(']);
        $braceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $braceOpenIndex);

        if ($braceCloseIndex < $index) {
            return;
        }

        $this->replaceToken($tokens, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens()
    {
        return [CT::T_TYPE_ALTERNATION];
    }

    private function replaceToken(Tokens $tokens, $index)
    {
        $tokens[$index] = new Token([CT::T_TYPE_ALTERNATION, '|']);
    }
}
