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

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class FunctionsAnalyzer
{
    /**
     * @param Tokens $tokens
     * @param int    $methodIndex
     *
     * @return array
     */
    public function getFunctionArguments(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $argumentAnalyzer = new ArgumentsAnalyzer();
        $arguments = [];

        foreach ($argumentAnalyzer->getArguments($tokens, $argumentsStart, $argumentsEnd) as $start => $end) {
            $argumentInfo = $argumentAnalyzer->getArgumentInfo($tokens, $start, $end);
            $arguments[$argumentInfo['name']] = $argumentInfo;
        }

        return $arguments;
    }

    /**
     * @param Tokens $tokens
     * @param int    $methodIndex
     *
     * @return array
     */
    public function getFunctionReturnType(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $typeColonIndex = $tokens->getNextMeaningfulToken($argumentsEnd);
        if (':' !== $tokens[$typeColonIndex]->getContent()) {
            return [];
        }

        $type = '';
        $typeStartIndex = $tokens->getNextNonWhitespace($typeColonIndex);
        $typeEndIndex = $typeStartIndex;
        $functionBodyStart = $tokens->getNextTokenOfKind($typeColonIndex, ['{', ';']);
        for ($i = $typeStartIndex; $i < $functionBodyStart; ++$i) {
            if ($tokens[$i]->isWhitespace()) {
                continue;
            }

            $type .= $tokens[$i]->getContent();
            $typeEndIndex = $i;
        }

        return [
            'type' => $type,
            'start_index' => $typeStartIndex,
            'end_index' => $typeEndIndex,
        ];
    }
}
