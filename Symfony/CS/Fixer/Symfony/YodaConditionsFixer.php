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
            if ($this->isHighComparison($tokens[$index])) {
                $this->fixHighComparison($tokens, $index);
            }
        }

        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            if ($this->isLowComparison($tokens[$index])) {
                $this->fixLowComparison($tokens, $index);
            }
        }
    }

    private function isHighComparison(Token $token)
    {
        if ($token->equalsAny(array('>', '<'))) {
            return true;
        }

        return $token->isGivenKind(array(
            T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL,
        ));
    }

    private function isLowComparison(Token $token)
    {
        return $token->isGivenKind(array(
            T_IS_EQUAL, T_IS_NOT_EQUAL,
            T_IS_IDENTICAL, T_IS_NOT_IDENTICAL,
        ));
    }

    private function fixHighComparison(Tokens $tokens, &$index)
    {
        $this->fixComparison($tokens, $index, true);
    }

    private function fixLowComparison(Tokens $tokens, &$index)
    {
        $this->fixComparison($tokens, $index, false);
    }

    private function fixComparison(Tokens $tokens, &$index, $high)
    {
        $startLeft = $this->findComparisonStart($tokens, $index, $high);
        $endLeft = $tokens->getPrevNonWhitespace($index);

        $startRight = $tokens->getNextNonWhitespace($index);
        $endRight = $this->findComparisonEnd($tokens, $index, $high);

        if ($this->isSimple($tokens, $startLeft, $endLeft)
                || !$this->isSimple($tokens, $startRight, $endRight)) {
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

        $this->turnAround($tokens[$index]);
        $tokens->insertAt($startRight, $left);
        $tokens->insertAt($startLeft, $right);

        $index = $startLeft;
    }

    private function turnAround(Token $token)
    {
        static $mapping = array(
            '<' => '>',
            '>' => '<',
            '<=' => '>=',
            '>=' => '<=',
        );

        if (isset($mapping[$token->getContent()])) {
            $token->setContent($mapping[$token->getContent()]);
        }
    }

    private function isSimple(Tokens $tokens, $start, $end)
    {
        if ($end !== $start) {
            return false;
        }

        return $tokens[$start]->isGivenKind(array(
            // numbers
            T_LNUMBER, T_DNUMBER,
            // string
            T_CONSTANT_ENCAPSED_STRING,
            // keywords
            T_STRING,
        ));
    }

    private function findComparisonStart(Tokens $tokens, $index, $high)
    {
        $level = 0;
        while (0 <= $index) {
            $token = $tokens[$index];

            if (0 === $level && $this->isTokenOfLowerPrecedence($token, $high)) {
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

    private function findComparisonEnd(Tokens $tokens, $index, $high)
    {
        $level = 0;
        $count = count($tokens);
        while ($index < $count) {
            $token = $tokens[$index];

            if (0 === $level && $this->isTokenOfLowerPrecedence($token, $high)) {
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

    private function isTokenOfLowerPrecedence(Token $token, $high)
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

        static $highTokens = array(
            // '==', '!=',
            T_IS_EQUAL, T_IS_NOT_EQUAL,
            // '===', '!==', '<>'
            T_IS_IDENTICAL, T_IS_NOT_IDENTICAL,
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

        if ($high) {
            if ($token->isGivenKind($highTokens)) {
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
