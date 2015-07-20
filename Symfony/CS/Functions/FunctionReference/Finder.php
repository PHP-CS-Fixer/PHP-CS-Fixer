<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Functions\FunctionReference;

use Symfony\CS\Tokenizer\Tokens;

final class Finder
{
    /**
     * Looks up Tokens sequence for suitable candidates and delivers boundaries information,
     * which can be supplied by Symfony\CS\Functions\*Util classes.
     *
     * @param string $functionNameToSearch
     * @param Tokens $tokens
     * @param int    $start
     * @param int    $end
     *
     * @return int[]|null returns $functionName, $openParenthesis, $closeParenthesis packed into array
     */
    public static function find($functionNameToSearch, Tokens $tokens, $start, $end)
    {
        // find raw sequence which we can analyse for context
        $candidateSequence = array(array(T_STRING, $functionNameToSearch), '(');
        $matches = $tokens->findSequence($candidateSequence, $start, $end, false);
        if (null === $matches) {
            // not found, simply return without further attempts
            return;
        }

        // translate results for humans
        list($functionName, $openParenthesis) = array_keys($matches);

        // first criteria check: shall look like function call
        $functionNamePrefix = $tokens->getPrevMeaningfulToken($functionName);
        $functionNamePrecedingToken = $tokens[$functionNamePrefix];
        if ($functionNamePrecedingToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
            // this expression is differs from expected, resume
            return self::find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }

        // second criteria check: ensure namespace is the root one
        if ($functionNamePrecedingToken->isGivenKind(T_NS_SEPARATOR)) {
            $namespaceCandidate = $tokens->getPrevMeaningfulToken($functionNamePrefix);
            $namespaceCandidateToken = $tokens[$namespaceCandidate];
            if ($namespaceCandidateToken->isGivenKind(array(T_NEW, T_STRING, CT_NAMESPACE_OPERATOR))) {
                // here can be added complete namespace scan
                // this expression is differs from expected, resume
                return self::find($functionNameToSearch, $tokens, $openParenthesis, $end);
            }
        }

        // final step: find closing parenthesis
        $closeParenthesis = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
        if (null === $closeParenthesis) {
            // this sequence is not closed, try resuming
            return self::find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }

        return array($functionName, $openParenthesis, $closeParenthesis);
    }
}
