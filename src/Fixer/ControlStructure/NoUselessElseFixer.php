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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoUselessElseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_ELSE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ELSE)) {
                continue;
            }

            // `else if` vs. `else` and alternative syntax `else:` checks
            if ($tokens[$tokens->getNextMeaningfulToken($index)]->equalsAny(array(':', array(T_IF)))) {
                continue;
            }

            // clean up `else` if it is an empty statement
            $this->fixEmptyElse($tokens, $index);
            if ($token->isEmpty()) {
                continue;
            }

            // clean up `else` if possible
            $this->fixElse($tokens, $index);
        }
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
        // should be run before NoDuplicateSemicolonsFixer, NoWhitespaceInBlankLinesFixer, NoExtraConsecutiveBlankLinesFixer and BracesFixer.
        return 25;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_ELSE index
     */
    private function fixElse(Tokens $tokens, $index)
    {
        $previousBlockStart = $index;
        do {
            // Check if all 'if', 'else if ' and 'elseif' blocks above this 'else' always end,
            // if so this 'else' is overcomplete.
            list($previousBlockStart, $previousBlockEnd) = $this->getPreviousBlock($tokens, $previousBlockStart);

            // short 'if' detection
            $previous = $previousBlockEnd;
            if ($tokens[$previous]->equals('}')) {
                $previous = $tokens->getPrevMeaningfulToken($previous);
            }

            if (
                !$tokens[$previous]->equals(';') ||                              // 'if' block doesn't end with semicolon, keep 'else'
                $tokens[$tokens->getPrevMeaningfulToken($previous)]->equals('{') // empty 'if' block, keep 'else'
            ) {
                return;
            }

            $candidateIndex = $tokens->getPrevTokenOfKind(
                $previous,
                array(
                    ';',
                    array(T_CLOSE_TAG),
                    array(T_IF),
                    array(T_BREAK),
                    array(T_CONTINUE),
                    array(T_EXIT),
                    array(T_GOTO),
                    array(T_RETURN),
                    array(T_THROW),
                )
            );

            if (
                null === $candidateIndex ||
                $tokens[$candidateIndex]->equalsAny(array(';', array(T_CLOSE_TAG), array(T_IF))) ||
                $this->isInConditional($tokens, $candidateIndex, $previousBlockStart)
            ) {
                return;
            }

            // implicit continue, i.e. delete candidate
        } while (!$tokens[$previousBlockStart]->isGivenKind(T_IF));

        // if we made it to here the 'else' can be removed
        $this->clearElse($tokens, $index);
    }

    /**
     * Return the first and last token index of the previous block.
     *
     * [0] First is either T_IF, T_ELSE or T_ELSEIF
     * [1] Last is either '}' or ';' / T_CLOSE_TAG for short notation blocks
     *
     * @param Tokens $tokens
     * @param int    $index  T_IF, T_ELSE, T_ELSEIF
     *
     * @return int[]
     */
    private function getPreviousBlock(Tokens $tokens, $index)
    {
        $close = $previous = $tokens->getPrevMeaningfulToken($index);
        // short 'if' detection
        if ($tokens[$close]->equals('}')) {
            $previous = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $close, false);
        }

        $open = $tokens->getPrevTokenOfKind($previous, array(array(T_IF), array(T_ELSE), array(T_ELSEIF)));
        if ($tokens[$open]->isGivenKind(T_IF)) {
            $elseCandidate = $tokens->getPrevMeaningfulToken($open);
            if ($tokens[$elseCandidate]->isGivenKind(T_ELSE)) {
                $open = $elseCandidate;
            }
        }

        return array($open, $close);
    }

    /**
     * Remove tokens part of an `else` statement if not empty (i.e. no meaningful tokens inside).
     *
     * @param Tokens $tokens
     * @param int    $index  T_ELSE index
     */
    private function fixEmptyElse(Tokens $tokens, $index)
    {
        $next = $tokens->getNextMeaningfulToken($index);
        if ($tokens[$next]->equals('{')) {
            $close = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $next);
            if (1 === $close - $next) { // '{}'
                $this->clearElse($tokens, $index);
            } elseif ($tokens->getNextMeaningfulToken($next) === $close) { // '{/**/}'
                $this->clearElse($tokens, $index);
            }

            return;
        }

        // short `else`
        $end = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));
        if ($next === $end) {
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

    /**
     * @param Tokens $tokens
     * @param int    $index           Index of the token to check
     * @param int    $lowerLimitIndex Lower limit index. Since the token to check will always be in a conditional we must stop checking at this index
     *
     * @return bool
     */
    private function isInConditional(Tokens $tokens, $index, $lowerLimitIndex)
    {
        $candidateIndex = $tokens->getPrevTokenOfKind($index, array(')', ';', ':'));
        if ($tokens[$candidateIndex]->equals(':')) {
            return true;
        }

        if (!$tokens[$candidateIndex]->equals(')')) {
            return false; // token is ';' or close tag
        }

        // token is always ')' here.
        // If it is part of the condition the token is always in, return false.
        // If it is not it is a nested condition so return true
        $open = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $candidateIndex, false);

        return $tokens->getPrevMeaningfulToken($open) > $lowerLimitIndex;
    }
}
