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

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Israel Shirk <israelshirk@gmail.com>
 */
final class CommonOperatorWhitespaceFixer extends AbstractFixer
{
    public function configure(array $configuration = null)
    {
        // We don't need to configure much
    }

    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {

        $matchers = array(
            T_BOOLEAN_AND,              //  &&  logical operators
            T_BOOLEAN_OR,               //  ||  logical operators
            T_AND_EQUAL,                //  &=  assignment operators
            T_CONCAT_EQUAL,             //  .=  assignment operators
            T_DIV_EQUAL,                //  /=  assignment operators
            T_IS_EQUAL,                 //  ==  comparison operators
            T_IS_GREATER_OR_EQUAL,      //  >=  comparison operators
            T_IS_IDENTICAL,             //  === comparison operators
            T_IS_NOT_EQUAL,             //  != or <>    comparison operators
            T_IS_NOT_IDENTICAL,         //  !== comparison operators
            T_IS_SMALLER_OR_EQUAL,      //  <=  comparison operators
            T_MINUS_EQUAL,              //  -=  assignment operators
            T_MOD_EQUAL,                //  %=  assignment operators
            T_MUL_EQUAL,                //  *=  assignment operators
            T_OR_EQUAL,                 //  |=  assignment operators
            T_PLUS_EQUAL,               //  +=  assignment operators
            T_POW_EQUAL,                //  **= assignment operators (available since PHP 5.6.0)
            T_SL_EQUAL,                 //  <<= assignment operators
            T_SR_EQUAL,                 //  >>= assignment operators
            T_XOR_EQUAL,                //  ^=  assignment operators
            T_DOUBLE_ARROW,             //  =>  array syntax
        );

        $contentMatchers = array(
            '='                         // = assignment operator
        );

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            foreach ($matchers as $kind) {
                if (!$token->isGivenKind($kind)) {
                    continue;
                }

                if (!$tokens[$index + 1]->isWhitespace()) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                }

                if (!$tokens[$index - 1]->isWhitespace()) {
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
                }
            }

            foreach ($contentMatchers as $content) {
                if ($token->getId() !== null || $token->getContent() !== $content) {
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
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Common math, comparison, and boolean operators should have at least one whitespace surrounding them.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the ConcatWithoutSpacesFixer
        return -10;
    }
}
