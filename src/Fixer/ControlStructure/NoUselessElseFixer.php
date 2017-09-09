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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'There should not be useless `else` cases.',
            [
                new CodeSample("<?php\nif (\$a) {\n    return 1;\n} else {\n    return 2;\n}"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before NoWhitespaceInBlankLineFixer, NoExtraConsecutiveBlankLinesFixer, BracesFixer and after NoEmptyStatementFixer.
        return 25;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ELSE)) {
                continue;
            }

            // `else if` vs. `else` and alternative syntax `else:` checks
            if ($tokens[$tokens->getNextMeaningfulToken($index)]->equalsAny([':', [T_IF]])) {
                continue;
            }

            // clean up `else` if it is an empty statement
            $this->fixEmptyElse($tokens, $index);
            if ($tokens->isEmptyAt($index)) {
                continue;
            }

            // clean up `else` if possible
            $this->fixElse($tokens, $index);
        }
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
                [
                    ';',
                    [T_BREAK],
                    [T_CLOSE_TAG],
                    [T_CONTINUE],
                    [T_EXIT],
                    [T_GOTO],
                    [T_IF],
                    [T_RETURN],
                    [T_THROW],
                ]
            );

            if (
                null === $candidateIndex
                || $tokens[$candidateIndex]->equalsAny([';', [T_CLOSE_TAG], [T_IF]])
                || $this->isInConditional($tokens, $candidateIndex, $previousBlockStart)
                || $this->isInConditionWithoutBraces($tokens, $candidateIndex, $previousBlockStart)
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

        $open = $tokens->getPrevTokenOfKind($previous, [[T_IF], [T_ELSE], [T_ELSEIF]]);
        if ($tokens[$open]->isGivenKind(T_IF)) {
            $elseCandidate = $tokens->getPrevMeaningfulToken($open);
            if ($tokens[$elseCandidate]->isGivenKind(T_ELSE)) {
                $open = $elseCandidate;
            }
        }

        return [$open, $close];
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
        $end = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);
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
        $candidateIndex = $tokens->getPrevTokenOfKind($index, [')', ';', ':']);
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

    /**
     * For internal use only, as it is not perfect.
     *
     * Returns if the token at given index is part of a if/elseif/else statement
     * without {}. Assumes not passing the last `;`/close tag of the statement, not
     * out of range index, etc.
     *
     * @param Tokens $tokens
     * @param int    $index           Index of the token to check
     * @param int    $lowerLimitIndex
     *
     * @return bool
     */
    private function isInConditionWithoutBraces(Tokens $tokens, $index, $lowerLimitIndex)
    {
        do {
            if ($tokens[$index]->isComment() || $tokens[$index]->isWhitespace()) {
                $index = $tokens->getPrevMeaningfulToken($index);
            }

            $token = $tokens[$index];
            if ($token->isGivenKind([T_IF, T_ELSEIF, T_ELSE])) {
                return true;
            }

            if ($token->equals(';', '}')) {
                return false;
            }
            if ($token->equals('{')) {
                $index = $tokens->getPrevMeaningfulToken($index);

                // OK if belongs to: for, do, while, foreach
                // Not OK if belongs to: if, else, elseif
                if ($tokens[$index]->isGivenKind(T_DO)) {
                    --$index;

                    continue;
                }

                if (!$tokens[$index]->equals(')')) {
                    return false; // like `else {`
                }

                $index = $tokens->findBlockEnd(
                    Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                    $index,
                    false
                );

                $index = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$index]->isGivenKind([T_IF, T_ELSEIF])) {
                    return false;
                }
            } elseif ($token->equals(')')) {
                $type = Tokens::detectBlockType($token);
                $index = $tokens->findBlockEnd(
                    $type['type'],
                    $index,
                    false
                );

                $index = $tokens->getPrevMeaningfulToken($index);
            } else {
                --$index;
            }
        } while ($index > $lowerLimitIndex);

        return false;
    }
}
