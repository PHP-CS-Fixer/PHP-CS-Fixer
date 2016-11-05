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
 * Transform discriminate overloaded square braces tokens.
 *
 * Performed transformations:
 * - in `[1, 2, 3]` into CT::T_ARRAY_SQUARE_BRACE_OPEN and CT::T_ARRAY_SQUARE_BRACE_CLOSE,
 * - in `[$a, $b, $c] = array(1, 2, 3)` into CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN and CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class SquareBraceTransformer extends AbstractTransformer
{
    private $cacheOfArraySquareBraceCloseIndex = null;

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens()
    {
        return array(
            CT::T_ARRAY_SQUARE_BRACE_OPEN,
            CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
            CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        // short array syntax was introduced in PHP 5.4, but the fixer is smart
        // enough to handel it even before 5.4
        return 50000;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        $this->cacheOfArraySquareBraceCloseIndex = null;

        $this->transformIntoArraySquareBrace($tokens, $token, $index);

        if (PHP_VERSION_ID >= 70100) {
            $this->transformIntoDestructuringSquareBrace($tokens, $token, $index);
        }
    }

    private function transformIntoArraySquareBrace(Tokens $tokens, Token $token, $index)
    {
        if (!$this->isShortArray($tokens, $index)) {
            return;
        }

        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);

        $token->override(array(CT::T_ARRAY_SQUARE_BRACE_OPEN, '['));
        $tokens[$endIndex]->override(array(CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']'));

        $this->cacheOfArraySquareBraceCloseIndex = $endIndex;
    }

    private function transformIntoDestructuringSquareBrace(Tokens $tokens, Token $token, $index)
    {
        if (
            null === $this->cacheOfArraySquareBraceCloseIndex
            || !$tokens[$tokens->getNextMeaningfulToken($this->cacheOfArraySquareBraceCloseIndex)]->equals('=')
        ) {
            return;
        }

        $token->override(array(CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, '['));
        $tokens[$this->cacheOfArraySquareBraceCloseIndex]->override(array(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, ']'));
    }

    /**
     * Check if token under given index is short array opening.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isShortArray(Tokens $tokens, $index)
    {
        static $disallowedPrevTokens = array(
            ')',
            ']',
            '}',
            '"',
            array(T_CONSTANT_ENCAPSED_STRING),
            array(T_STRING),
            array(T_STRING_VARNAME),
            array(T_VARIABLE),
            array(CT::T_ARRAY_SQUARE_BRACE_CLOSE),
            array(CT::T_DYNAMIC_PROP_BRACE_CLOSE),
            array(CT::T_DYNAMIC_VAR_BRACE_CLOSE),
            array(CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE),
        );

        $token = $tokens[$index];

        if (!$token->equals('[')) {
            return false;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if (!$prevToken->equalsAny($disallowedPrevTokens)) {
            return true;
        }

        return false;
    }
}
