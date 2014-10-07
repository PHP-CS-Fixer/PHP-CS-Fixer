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
                if ($expectString && $current->equals('{')) {
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

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

    private function findComparisonStart(Tokens $tokens, $index)
    {
        static $blockTypes = array(
            ')' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            ']' => Tokens::BLOCK_TYPE_SQUARE_BRACE,
            '}' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        );

        while (0 <= $index) {
            $token = $tokens[$index];

            if ($this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            if ($token->equalsAny(array(')', ']', '}'))) {
                $index = $tokens->findBlockEnd($blockTypes[$token->getContent()], $index, false) - 1;
            } elseif ($token->equalsAny(array('(', '[', '{'))) {
                break;
            } else {
                --$index;
            }
        }

        return $tokens->getNextNonWhitespace($index);
    }

    private function findComparisonEnd(Tokens $tokens, $index)
    {
        static $blockTypes = array(
            '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            '[' => Tokens::BLOCK_TYPE_SQUARE_BRACE,
            '{' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        );

        $count = count($tokens);
        while ($index < $count) {
            $token = $tokens[$index];

            if ($this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            if ($token->equalsAny(array('(', '[', '{'))) {
                $index = $tokens->findBlockEnd($blockTypes[$token->getContent()], $index) + 1;
            } elseif ($token->equalsAny(array(')', ']', '}'))) {
                break;
            } else {
                ++$index;
            }
        }

        return $tokens->getPrevNonWhitespace($index);
    }

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
