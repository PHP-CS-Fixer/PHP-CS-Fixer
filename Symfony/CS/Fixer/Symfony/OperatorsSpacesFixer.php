<?php

/*
 * This file is part of the PHP CS utility.
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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OperatorsSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$this->isOperator($token)) {
                continue;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            if (!$tokens[$index - 1]->isWhitespace()) {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Operators should be arounded by at least one space.';
    }

    private function isOperator(Token $token)
    {
        static $arrayOperators = array(
            T_AND_EQUAL             => true,    // &=
            T_BOOLEAN_AND           => true,    // &&
            T_BOOLEAN_OR            => true,    // ||
            T_CONCAT_EQUAL          => true,    // .=
            T_DIV_EQUAL             => true,    // /=
            T_DOUBLE_ARROW          => true,    // =>
            T_IS_EQUAL              => true,    // ==
            T_IS_GREATER_OR_EQUAL   => true,    // >=
            T_IS_IDENTICAL          => true,    // ===
            T_IS_NOT_EQUAL          => true,    // !=, <>
            T_IS_NOT_IDENTICAL      => true,    // !==
            T_IS_SMALLER_OR_EQUAL   => true,    // <=
            T_LOGICAL_AND           => true,    // and
            T_LOGICAL_OR            => true,    // or
            T_LOGICAL_XOR           => true,    // xor
            T_MINUS_EQUAL           => true,    // -=
            T_MOD_EQUAL             => true,    // %=
            T_MUL_EQUAL             => true,    // *=
            T_OR_EQUAL              => true,    // |=
            T_PLUS_EQUAL            => true,    // +=
            T_SL                    => true,    // <<
            T_SL_EQUAL              => true,    // <<=
            T_SR                    => true,    // >>
            T_SR_EQUAL              => true,    // >>=
            T_XOR_EQUAL             => true,    // ^=
        );

        static $nonArrayOperators = array(
            '=' => true,
        );

        return $token->isArray() ? isset($arrayOperators[$token->getId()]) : isset($nonArrayOperators[$token->getContent()]);
    }
}
