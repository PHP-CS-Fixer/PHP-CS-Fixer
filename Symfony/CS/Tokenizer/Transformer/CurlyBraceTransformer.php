<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformer;

use Symfony\CS\Tokenizer\AbstractTransformer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform discriminate overloaded curly braces tokens.
 *
 * Performed transformations:
 * - closing `}` for T_CURLY_OPEN into CT_CURLY_CLOSE,
 * - closing `}` for T_DOLLAR_OPEN_CURLY_BRACES into CT_DOLLAR_CLOSE_CURLY_BRACES,
 * - in `$foo->{$bar}` into CT_DYNAMIC_PROP_BRACE_OPEN and CT_DYNAMIC_PROP_BRACE_CLOSE,
 * - in `${$foo}` into CT_DYNAMIC_VAR_BRACE_OPEN and CT_DYNAMIC_VAR_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class CurlyBraceTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        $this->transformIntoCurlyClose($tokens);

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_DOLLAR_OPEN_CURLY_BRACES)) {
                $nextIndex = $tokens->getNextTokenOfKind($index, array('}'));
                $tokens[$nextIndex]->override(array(CT_DOLLAR_CLOSE_CURLY_BRACES, '}'));

                continue;
            }

            if ($token->isGivenKind(T_OBJECT_OPERATOR)) {
                if (!$tokens[$index + 1]->equals('{')) {
                    continue;
                }

                $openIndex = $index + 1;
                $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);

                $tokens[$openIndex]->override(array(CT_DYNAMIC_PROP_BRACE_OPEN, '{'));
                $tokens[$closeIndex]->override(array(CT_DYNAMIC_PROP_BRACE_CLOSE, '}'));

                continue;
            }

            if ($token->equals('$')) {
                $openIndex = $tokens->getNextMeaningfulToken($index);

                if (null === $openIndex) {
                    continue;
                }

                $openToken = $tokens[$openIndex];

                if (!$openToken->equals('{')) {
                    continue;
                }

                $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);
                $closeToken = $tokens[$closeIndex];

                $openToken->override(array(CT_DYNAMIC_VAR_BRACE_OPEN, '{'));
                $closeToken->override(array(CT_DYNAMIC_VAR_BRACE_CLOSE, '}'));
            }
        }
    }

    /**
     * Transform closing `}` for T_CURLY_OPEN into CT_CURLY_CLOSE.
     *
     * This should be done at very beginning of curly braces transformations.
     *
     * @param Tokens $tokens
     */
    private function transformIntoCurlyClose(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CURLY_OPEN)) {
                continue;
            }

            $level = 1;
            $nestIndex = $index;

            while (0 < $level) {
                ++$nestIndex;

                // we count all kind of {
                if ($tokens[$nestIndex]->equals('{')) {
                    ++$level;
                    continue;
                }

                // we count all kind of }
                if ($tokens[$nestIndex]->equals('}')) {
                    --$level;
                }
            }

            $tokens[$nestIndex]->override(array(CT_CURLY_CLOSE, '}'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array(
            'CT_CURLY_CLOSE',
            'CT_DOLLAR_CLOSE_CURLY_BRACES',
            'CT_DYNAMIC_PROP_BRACE_OPEN',
            'CT_DYNAMIC_PROP_BRACE_CLOSE',
            'CT_DYNAMIC_VAR_BRACE_OPEN',
            'CT_DYNAMIC_VAR_BRACE_CLOSE',
        );
    }
}
