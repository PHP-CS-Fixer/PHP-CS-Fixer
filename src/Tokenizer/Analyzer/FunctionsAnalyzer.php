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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class FunctionsAnalyzer
{
    /**
     * @param int $index
     *
     * @return bool
     */
    public function isGlobalFunctionCall(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return false;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
            $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
        }

        $nextIndex = $tokens->getNextMeaningfulToken($index);

        return !$tokens[$prevIndex]->isGivenKind([T_DOUBLE_COLON, T_FUNCTION, CT::T_NAMESPACE_OPERATOR, T_NEW, T_OBJECT_OPERATOR, CT::T_RETURN_REF, T_STRING])
            && $tokens[$nextIndex]->equals('(');
    }

    /**
     * @param int $methodIndex
     *
     * @return ArgumentAnalysis[]
     */
    public function getFunctionArguments(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $argumentAnalyzer = new ArgumentsAnalyzer();
        $arguments = [];

        foreach ($argumentAnalyzer->getArguments($tokens, $argumentsStart, $argumentsEnd) as $start => $end) {
            $argumentInfo = $argumentAnalyzer->getArgumentInfo($tokens, $start, $end);
            $arguments[$argumentInfo->getName()] = $argumentInfo;
        }

        return $arguments;
    }

    /**
     * @param int $methodIndex
     *
     * @return null|TypeAnalysis
     */
    public function getFunctionReturnType(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $typeColonIndex = $tokens->getNextMeaningfulToken($argumentsEnd);
        if (':' !== $tokens[$typeColonIndex]->getContent()) {
            return null;
        }

        $type = '';
        $typeStartIndex = $tokens->getNextMeaningfulToken($typeColonIndex);
        $typeEndIndex = $typeStartIndex;
        $functionBodyStart = $tokens->getNextTokenOfKind($typeColonIndex, ['{', ';', [T_DOUBLE_ARROW]]);
        for ($i = $typeStartIndex; $i < $functionBodyStart; ++$i) {
            if ($tokens[$i]->isWhitespace() || $tokens[$i]->isComment()) {
                continue;
            }

            $type .= $tokens[$i]->getContent();
            $typeEndIndex = $i;
        }

        return new TypeAnalysis($type, $typeStartIndex, $typeEndIndex);
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function isTheSameClassCall(Tokens $tokens, $index)
    {
        if (!$tokens->offsetExists($index)) {
            return false;
        }

        $operatorIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens->offsetExists($operatorIndex)) {
            return false;
        }

        $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);
        if (!$tokens->offsetExists($referenceIndex)) {
            return false;
        }

        return $tokens[$operatorIndex]->equals([T_OBJECT_OPERATOR, '->']) && $tokens[$referenceIndex]->equals([T_VARIABLE, '$this'], false)
            || $tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STRING, 'self'], false)
            || $tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STATIC, 'static'], false);
    }
}
