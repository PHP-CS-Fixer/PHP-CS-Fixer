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
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            if ($this->isEqualComparison($tokens[$index])) {
                $this->fixComparison($tokens, $index);
            }
        }
    }

    private function isEqualComparison(Token $token)
    {
        return $token->isGivenKind(array(
            T_IS_EQUAL, T_IS_IDENTICAL,
        ));
    }

    private function fixComparison(Tokens $tokens, &$index)
    {
        $startLeft = $this->findComparisonStart($tokens, $index);
        $endLeft = $tokens->getPrevNonWhitespace($index);

        $startRight = $tokens->getNextNonWhitespace($index);
        $endRight = $this->findComparisonEnd($tokens, $index);

        if (!$this->isVariable($tokens, $startLeft, $endLeft)
                || $this->isVariable($tokens, $startRight, $endRight)) {
            // already using Yoda conditions, or impossible to write Yoda-style
            return;
        }

        $left = $tokens->generatePartialCode($startLeft, $endLeft);
        $left = Tokens::fromCode('<?php '.$left);
        $left[0]->clear();

        $right = $tokens->generatePartialCode($startRight, $endRight);
        $right = Tokens::fromCode('<?php '.$right);
        $right[0]->clear();

        $this->fixTokens($left);
        $this->fixTokens($right);

        for ($i = $startLeft; $i <= $endLeft; ++$i) {
            $tokens[$i]->clear();
        }

        for ($i = $startRight; $i <= $endRight; ++$i) {
            $tokens[$i]->clear();
        }

        $tokens->insertAt($startRight, $left);
        $tokens->insertAt($startLeft, $right);

        $index = $startLeft;
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
        $level = 0;
        while (0 <= $index) {
            $token = $tokens[$index];

            if (0 === $level && $this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            if ($token->equals(')')) {
                ++$level;
            } elseif ($token->equals('(')) {
                --$level;

                if (0 > $level) {
                    break;
                }
            }

            --$index;
        }

        return $tokens->getNextNonWhitespace($index);
    }

    private function findComparisonEnd(Tokens $tokens, $index)
    {
        $level = 0;
        $count = count($tokens);
        while ($index < $count) {
            $token = $tokens[$index];

            if (0 === $level && $this->isTokenOfLowerPrecedence($token)) {
                break;
            }

            if ($token->equals('(')) {
                ++$level;
            } elseif ($token->equals(')')) {
                --$level;

                if (0 > $level) {
                    break;
                }
            }

            ++$index;
        }

        return $tokens->getPrevNonWhitespace($index);
    }

    private function isTokenOfLowerPrecedence(Token $token)
    {
        static $tokens = array(
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

        static $nonTokens = array(
            '&', '|', '^',
            '?', ':',
            '=',
        );

        if ($token->isGivenKind($tokens) || $token->equalsAny($nonTokens)) {
            return true;
        }

        // PHP 5.6 introduced **=
        if (defined('T_POW_EQUAL')) {
            if ($token->isGivenKind(constant('T_POW_EQUAL'))) {
                return true;
            }
        }

        return $token->equalsAny(array(',', ';'));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Comparisons should be done using Yoda conditions.';
    }
}
