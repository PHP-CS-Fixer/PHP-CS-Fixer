<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\CS\Tokenizer\Tokens;

/**
 * @internal base class for function reference fixers
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
abstract class AbstractFunctionReferenceFixer extends AbstractFixer
{
    /**
     * Looks up Tokens sequence for suitable candidates and delivers boundaries information,
     * which can be supplied by other methods in this abstract class.
     *
     * @param string $functionNameToSearch
     * @param Tokens $tokens
     * @param int    $start
     * @param int    $end
     *
     * @return int[]|null returns $functionName, $openParenthesis, $closeParenthesis packed into array
     */
    protected function find($functionNameToSearch, Tokens $tokens, $start, $end)
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
            return $this->find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }

        // second criteria check: ensure namespace is the root one
        if ($functionNamePrecedingToken->isGivenKind(T_NS_SEPARATOR)) {
            $namespaceCandidate = $tokens->getPrevMeaningfulToken($functionNamePrefix);
            $namespaceCandidateToken = $tokens[$namespaceCandidate];
            if ($namespaceCandidateToken->isGivenKind(array(T_NEW, T_STRING, CT_NAMESPACE_OPERATOR))) {
                // here can be added complete namespace scan
                // this expression is differs from expected, resume
                return $this->find($functionNameToSearch, $tokens, $openParenthesis, $end);
            }
        }

        // final step: find closing parenthesis
        $closeParenthesis = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
        if (null === $closeParenthesis) {
            // this sequence is not closed, try resuming
            return $this->find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }

        return array($functionName, $openParenthesis, $closeParenthesis);
    }

    /**
     * Count amount of parameters in a function/method reference.
     *
     * @param int            $openParenthesis
     * @param int            $closeParenthesis
     * @param Tokens $tokens
     *
     * @return int
     */
    protected function countArguments($openParenthesis, $closeParenthesis, Tokens $tokens)
    {
        $firstSensibleToken = $tokens->getNextMeaningfulToken($openParenthesis);
        if ($tokens[$firstSensibleToken]->equals(')')) {
            return 0;
        }

        $argumentsCount = 1;
        for ($paramContentIndex = $openParenthesis + 1; $paramContentIndex < $closeParenthesis; ++$paramContentIndex) {
            // skip nested (...) constructs
            if ($tokens[$paramContentIndex]->equals('(')) {
                $paramContentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $paramContentIndex);
                continue;
            }
            // skip nested [...] constructs
            if ($tokens[$paramContentIndex]->equals('[')) {
                $paramContentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $paramContentIndex);
                continue;
            }
            // skip nested {...} constructs
            if ($tokens[$paramContentIndex]->equals('{')) {
                $paramContentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $paramContentIndex);
                continue;
            }

            // if comma matched, increase arguments counter
            if ($tokens[$paramContentIndex]->equals(',')) {
                ++$argumentsCount;
            }
        }

        return $argumentsCount;
    }

    /**
     * Checks if function/method defined in the scope.
     *
     * @param string $functionName
     * @param Tokens $tokens
     *
     * @return bool
     */
    protected function isDefinedInScope($functionName, Tokens $tokens)
    {
        $definitionSequence = array(array(T_FUNCTION, 'function'), array(T_STRING, $functionName));

        $matchedDefinition = $tokens->findSequence($definitionSequence, 0, $tokens->count() - 1, false);

        return null !== $matchedDefinition;
    }
}
