<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Bram Gotink <bram@gotink.me>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class YodaConditionsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixTokens($tokens);

        return $tokens->generateCode();
    }

    /**
     * Fixes the comparisons in the given tokens.
     *
     * @param Tokens $tokens The token list to fix
     */
    private function fixTokens(Tokens $tokens)
    {
        $comparisons = $tokens->findGivenKind(array(T_IS_EQUAL, T_IS_IDENTICAL));
        $comparisons = array_merge(array_keys($comparisons[T_IS_EQUAL]), array_keys($comparisons[T_IS_IDENTICAL]));
        sort($comparisons);

        $lastFixedIndex = count($tokens);

        foreach (array_reverse($comparisons) as $index) {
            if ($index >= $lastFixedIndex) {
                continue;
            }

            $lastFixedIndex = $this->fixComparison($tokens, $index);
        }
    }

    /**
     * Fixes the comparison at the given index.
     *
     * A comparison is considered fixed when
     * - both sides are a variable (e.g. $a === $b)
     * - neither side is a variable (e.g. self::CONST === 3)
     * - only the right-hand side is a variable (e.g. 3 === self::$var)
     *
     * If the left-hand side and right-hand side of the given comparison are
     * swapped, this function runs recursively on the previous left-hand-side.
     *
     * @param Tokens $tokens The token list
     * @param int    $index  The index of the comparison to fix
     *
     * @return int A upper bound for all non-fixed comparisons.
     */
    private function fixComparison(Tokens $tokens, $index)
    {
        $startLeft = $this->findComparisonStart($tokens, $index);
        $endLeft = $tokens->getPrevNonWhitespace($index);

        $startRight = $tokens->getNextNonWhitespace($index);
        $endRight = $this->findComparisonEnd($tokens, $index);

        if (!$this->isVariable($tokens, $startLeft, $endLeft)
                || $this->isVariable($tokens, $startRight, $endRight)) {
            // already using Yoda conditions, or impossible to write Yoda-style
            return $index;
        }

        $left = $tokens->generatePartialCode($startLeft, $endLeft);
        $left = Tokens::fromCode('<?php '.$left);
        $left[0]->clear();

        $right = $tokens->generatePartialCode($startRight, $endRight);
        $right = Tokens::fromCode('<?php '.$right);
        $right[0]->clear();

        $this->fixTokens($left);

        for ($i = $startLeft; $i <= $endLeft; ++$i) {
            $tokens[$i]->clear();
        }

        for ($i = $startRight; $i <= $endRight; ++$i) {
            $tokens[$i]->clear();
        }

        $tokens->insertAt($startRight, $left);
        $tokens->insertAt($startLeft, $right);

        return $startLeft;
    }

    /**
     * Checks whether the tokens between the given start and end describe a
     * variable.
     *
     * @param Tokens $tokens The token list
     * @param int    $start  The first index of the possible variable
     * @param int    $end    The last index of the possible varaible
     *
     * @return bool Whether the tokens describe a variable
     */
    private function isVariable(Tokens $tokens, $start, $end)
    {
        if ($end === $start) {
            return $tokens[$start]->isGivenKind(T_VARIABLE);
        }

        $index = $start;
        $expectString = false;
        while ($index <= $end) {
            $current = $tokens[$index];
            if ($index < $end) {
                $next = $tokens[$index + 1];

                // self:: or ClassName::
                if ($current->isGivenKind(T_STRING) && $next->isGivenKind(T_DOUBLE_COLON)) {
                    $index += 2;
                    continue;
                }

                // \ClassName
                if ($current->isGivenKind(T_NS_SEPARATOR) && $next->isGivenKind(T_STRING)) {
                    ++$index;
                    continue;
                }

                // ClassName\
                if ($current->isGivenKind(T_STRING) && $next->isGivenKind(T_NS_SEPARATOR)) {
                    $index += 2;
                    continue;
                }

                // $a-> or a-> (as in $b->a->c)
                if ($current->isGivenKind($expectString ? T_STRING : T_VARIABLE) && $next->isGivenKind(T_OBJECT_OPERATOR)) {
                    $index += 2;
                    $expectString = true;
                    continue;
                }

                // {...} (as in $a->{$b})
                if ($expectString && $current->isGivenKind(CT_DYNAMIC_PROP_BRACE_OPEN)) {
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DYNAMIC_PROP_BRACE, $index);

                    if ($index === $end) {
                        return true;
                    } elseif ($index > $end) {
                        return false;
                    }

                    ++$index;

                    if (!$tokens[$index]->isGivenKind(T_OBJECT_OPERATOR)) {
                        return false;
                    }
                    ++$index;

                    continue;
                }

                // $a[...] or a[...] (as in $c->a[$b])
                if ($current->isGivenKind($expectString ? T_STRING : T_VARIABLE) && $next->equals('[')) {
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $index + 1);

                    if ($index === $end) {
                        return true;
                    } elseif ($index > $end) {
                        return false;
                    }

                    ++$index;

                    if (!$tokens[$index]->isGivenKind(T_OBJECT_OPERATOR)) {
                        return false;
                    }
                    ++$index;

                    $expectString = true;
                    continue;
                }

                return false;
            } else {
                // this is the last token!
                return $current->isGivenKind($expectString ? T_STRING : T_VARIABLE);
            }
        }

        return false;
    }

    /**
     * Finds the start of the left-hand side of the comparison at the given
     * index.
     *
     * The left-hand side ends when an operator with a lower precedence is
     * encountered or when the block level for `()`, `{}` or `[]` goes below
     * zero.
     *
     * @param Tokens $tokens The token list
     * @param int    $index  The index of the comparison
     *
     * @return int The first index of the left-hand side of the comparison
     */
    private function findComparisonStart(Tokens $tokens, $index)
    {
        while (0 <= $index) {
            $token = $tokens[$index];

            if ($this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            $block = $tokens->detectBlockType($token);

            if (null === $block) {
                --$index;
                continue;
            }

            if ($block['isStart']) {
                break;
            }

            $index = $tokens->findBlockEnd($block['type'], $index, false) - 1;
        }

        return $tokens->getNextNonWhitespace($index);
    }

    /**
     * Finds the end of the right-hand side of the comparison at the given
     * index.
     *
     * The right-hand side ends when an operator with a lower precedence is
     * encountered or when the block level for `()`, `{}` or `[]` goes below
     * zero.
     *
     * @param Tokens $tokens The token list
     * @param int    $index  The index of the comparison
     *
     * @return int The last index of the right-hand side of the comparison
     */
    private function findComparisonEnd(Tokens $tokens, $index)
    {
        $count = count($tokens);

        while ($index < $count) {
            $token = $tokens[$index];

            if ($this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            $block = $tokens->detectBlockType($token);

            if (null === $block) {
                ++$index;
                continue;
            }

            if (!$block['isStart']) {
                break;
            }

            $index = $tokens->findBlockEnd($block['type'], $index) + 1;
        }

        return $tokens->getPrevNonWhitespace($index);
    }

    /**
     * Checks whether the given token has a lower precedence than `T_IS_EQUAL`
     * or `T_IS_IDENTICAL`.
     *
     * @param Token $token The token to check
     *
     * @return bool Whether the token has a lower precedence
     */
    private function isTokenOfLowerPrecedence(Token $token)
    {
        static $tokens;

        if (null === $tokens) {
            $tokens = array(
                // '&&', '||',
                T_BOOLEAN_AND, T_BOOLEAN_OR,
                // '.=', '/=', '-=', '%=', '*=', '+=',
                T_CONCAT_EQUAL, T_DIV_EQUAL, T_MINUS_EQUAL, T_MUL_EQUAL, T_PLUS_EQUAL,
                // '&=', '|=', '^=',
                T_AND_EQUAL, T_OR_EQUAL, T_XOR_EQUAL,
                // '<<=', '>>=', '=>',
                T_SL_EQUAL, T_SR_EQUAL, T_DOUBLE_ARROW,
                // 'and', 'or', 'xor',
                T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR,
                // keywords like 'return'
                T_RETURN, T_THROW, T_GOTO, T_CASE,
            );

            // PHP 5.6 introduced **=
            if (defined('T_POW_EQUAL')) {
                $tokens[] = constant('T_POW_EQUAL');
            }
        }

        static $otherTokens = array(
            // bitwise and, or, xor
            '&', '|', '^',
            // ternary operators
            '?', ':',
            // assignment
            '=',
            // end of PHP statement
            ',', ';',
        );

        return $token->isGivenKind($tokens) || $token->equalsAny($otherTokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Comparisons should be done using Yoda conditions.';
    }
}
