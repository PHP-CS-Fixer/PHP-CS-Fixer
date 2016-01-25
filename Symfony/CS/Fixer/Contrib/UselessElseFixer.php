<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractAlignFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
class UselessElseFixer extends AbstractAlignFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        // start from 4; minimum number of tokens that form a candidate for fixing; <?php if ( [] ) [5]
        $elseTokens = $tokens->findGivenKind(T_ELSE, 4);
        foreach ($elseTokens as $index => $elseToken) {
            // `else if` vs. `else` check
            if ($tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_IF)) {
                continue;
            }

            // clean up `else` if possible
            $this->fixElse($tokens, $index);

            // clean up `else` if it is an empty statement
            if (!$tokens[$index]->isEmpty()) {
                $this->fixEmptyElse($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be useless else cases.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before DuplicateSemicolonFixer, WhitespacyLinesFixer, ExtraEmptyLinesFixer and BracesFixer
        return 15;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_ELSE index
     */
    private function fixElse(Tokens $tokens, $index)
    {
        // check if all 'if', 'else if ' and 'elseif' blocks above the 'else' always end,
        // which would mean the else T_CASE being overcomplete.

        $previous = $tokens->getPrevMeaningfulToken($index);

        // short 'if' detection
        if ($tokens[$previous]->equals('}')) {
            $previous = $tokens->getPrevMeaningfulToken($previous);
        }

        // empty 'if' block
        if (!$tokens[$previous]->equals(';')) {
            return;
        }

        // empty 'if' block
        $previous = $tokens->getPrevMeaningfulToken($previous);
        if ($tokens[$previous]->equalsAny(array('{', '}'))) {
            return;
        }

        // 'break;' 'continue;' 'exit;' 'die;' 'return;' before 'else'
        if ($tokens[$previous]->isGivenKind(array(T_BREAK, T_CONTINUE, T_EXIT, T_RETURN))) {
            $this->clearElse($tokens, $index);

            return;
        }

        $candidateIndex = $tokens->getTokenOfKindSibling(
            $previous,
            -1,
            array(
                ';',
                '}',
                array(T_BREAK),
                array(T_CONTINUE),
                array(T_EXIT),
                array(T_GOTO),
                array(T_RETURN),
                array(T_THROW),
            )
        );

        if (null !== $candidateIndex && !$tokens[$candidateIndex]->equalsAny(array(';', '}'))) {
            $this->clearElse($tokens, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_ELSE index
     */
    private function fixEmptyElse(Tokens $tokens, $index)
    {
        $next = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$next]->equals('{')) {
            return;
        }

        $close = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $next);
        if (1 === $close - $next) {
            $this->clearElse($tokens, $index);

            return;
        }

        $nextNext = $tokens->getNextMeaningfulToken($next);
        if ($nextNext === $close) {
            $this->clearElse($tokens, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of T_ELSE
     */
    private function clearElse(Tokens $tokens, $index)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);

        // clear T_ELSE and the '{' '}' if there are any
        $next = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$next]->equals('{')) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $next));
        $tokens->clearTokenAndMergeSurroundingWhitespace($next);
    }
}
