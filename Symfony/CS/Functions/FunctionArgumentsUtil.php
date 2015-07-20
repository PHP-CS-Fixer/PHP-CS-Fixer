<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Functions;

use Symfony\CS\Tokenizer\Tokens;

final class FunctionArgumentsUtil
{
    /**
     * Count amount of parameters in a function/method reference.
     *
     * @param int                                  $openParenthesis
     * @param int                                  $closeParenthesis
     * @param Tokens|\Symfony\CS\Tokenizer\Token[] $tokens
     *
     * @return int
     */
    public static function gerArgumentsCount($openParenthesis, $closeParenthesis, Tokens $tokens)
    {
        $firstSensibleToken = $tokens->getNextMeaningfulToken($openParenthesis);
        if ('(' === $tokens[$firstSensibleToken]->getContent()) {
            return 0;
        }

        $argumentsCount = 1;
        for ($paramContentIndex = $openParenthesis + 1; $paramContentIndex < $closeParenthesis; ++$paramContentIndex) {
            // skip nested (...) constructs
            if ('(' === $tokens[$paramContentIndex]->getContent()) {
                $paramContentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $paramContentIndex);
                continue;
            }

            // if comma matched, increase arguments counter
            if (',' === $tokens[$paramContentIndex]->getContent()) {
                ++$argumentsCount;
            }
        }

        return $argumentsCount;
    }
}
