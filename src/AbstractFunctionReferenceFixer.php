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

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * Base class for function reference fixers.
 *
 * @internal
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
abstract class AbstractFunctionReferenceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * Looks up Tokens sequence for suitable candidates and delivers boundaries information,
     * which can be supplied by other methods in this abstract class.
     *
     * @param string   $functionNameToSearch
     * @param Tokens   $tokens
     * @param int      $start
     * @param int|null $end
     *
     * @return int[]|null returns $functionName, $openParenthesis, $closeParenthesis packed into array
     */
    protected function find($functionNameToSearch, Tokens $tokens, $start = 0, $end = null)
    {
        // make interface consistent with findSequence
        $end = null === $end ? $tokens->count() : $end;

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
     * @param Tokens $tokens
     * @param int    $openParenthesis
     * @param int    $closeParenthesis
     *
     * @return int
     */
    protected function countArguments(Tokens $tokens, $openParenthesis, $closeParenthesis)
    {
        $firstSensibleToken = $tokens->getNextMeaningfulToken($openParenthesis);
        if ($tokens[$firstSensibleToken]->equals(')')) {
            return 0;
        }

        $argumentsCount = 1;
        for ($paramContentIndex = $openParenthesis + 1; $paramContentIndex < $closeParenthesis; ++$paramContentIndex) {
            $token = $tokens[$paramContentIndex];

            // skip nested (), [], {} constructs
            $blockDefinitionProbe = Tokens::detectBlockType($token);
            if (null !== $blockDefinitionProbe && true === $blockDefinitionProbe['isStart']) {
                $paramContentIndex = $tokens->findBlockEnd($blockDefinitionProbe['type'], $paramContentIndex);
                continue;
            }

            // if comma matched, increase arguments counter
            if ($token->equals(',')) {
                ++$argumentsCount;
            }
        }

        return $argumentsCount;
    }
}
