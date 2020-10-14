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

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
final class VariableAnalyzer
{
    /**
     * @var FunctionsAnalyzer
     */
    private $functionAnalyzer;

    /**
     * @var ArgumentsAnalyzer
     */
    private $argumentsAnalyzer;

    /**
     * Removes the variables that are (possibly) used with the given range.
     *
     * @param int                $startIndex
     * @param int                $endIndex
     * @param array<string, int> $variableNames variable name (including dollar) => index
     *
     * @return array<string, int>
     */
    public function filterVariablePossiblyUsed(Tokens $tokens, $startIndex, $endIndex, array $variableNames)
    {
        $functionAnalyzer = $this->getFunctionsAnalyzer();
        $argumentsAnalyzer = $this->getArgumentsAnalyzer();
        $tokensAnalyzer = $this->getTokensAnalyzer($tokens);

        static $riskyKinds = [
            CT::T_DYNAMIC_VAR_BRACE_OPEN,
            T_EVAL,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
        ];

        $variableNames = array_filter(
            $variableNames,
            static function ($variableIndex) use ($tokens, $tokensAnalyzer) {
                if (!$tokens[$variableIndex]->isGivenKind(T_VARIABLE)) {
                    throw new \InvalidArgumentException(sprintf('Not a variable at %d.', $variableIndex));
                }

                return !$tokensAnalyzer->isSuperGlobal($variableIndex);
            }
        );

        if (0 === \count($variableNames)) {
            return $variableNames;
        }

        for ($index = $startIndex; $index < $endIndex; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_STRING) && 'compact' === strtolower($token->getContent()) && $functionAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                return []; // wouldn't touch it with a ten-foot pole
            }

            if ($token->isGivenKind($riskyKinds)) {
                return [];
            }

            if ($token->equals('$')) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);

                if ($tokens[$nextIndex]->isGivenKind(T_VARIABLE)) {
                    return []; // "$$a" case, can be any variable
                }
            }

            if ($token->isGivenKind(T_VARIABLE)) {
                $content = $token->getContent();

                if (isset($variableNames[$content])) {
                    unset($variableNames[$content]);

                    if (0 === \count($variableNames)) {
                        return $variableNames;
                    }
                }

                if ($tokensAnalyzer->isSuperGlobal($index)) {
                    return [];
                }
            }

            if ($token->isGivenKind(T_STRING_VARNAME)) {
                $content = '$'.$token->getContent();

                if (isset($variableNames[$content])) {
                    unset($variableNames[$content]);

                    if (0 === \count($variableNames)) {
                        return $variableNames;
                    }
                }
            }

            if ($token->isClassy()) { // check if used as argument in the constructor of an anonymous class
                $index = $tokens->getNextTokenOfKind($index, ['(', '{']);

                if ($tokens[$index]->equals('(')) {
                    $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                    $arguments = $argumentsAnalyzer->getArguments($tokens, $index, $closeBraceIndex);

                    $variableNames = $this->filterVariablesUsedAsArgument($tokens, $argumentsAnalyzer, $variableNames, $arguments);

                    $index = $tokens->getNextTokenOfKind($closeBraceIndex, ['{']);
                }

                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index); // skip body

                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) { // check if used as import
                $fnIndex = $index;
                $index = $tokens->getNextTokenOfKind($index, [[CT::T_USE_LAMBDA], '{']);

                if ($tokensAnalyzer->isLambda($fnIndex) && $tokens[$index]->isGivenKind(CT::T_USE_LAMBDA)) {
                    $functionOpenBraceIndex = $tokens->getNextTokenOfKind($index, ['(']);
                    $functionCloseBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $functionOpenBraceIndex);
                    $arguments = $argumentsAnalyzer->getArguments($tokens, $functionOpenBraceIndex, $functionCloseBraceIndex);

                    $variableNames = $this->filterVariablesUsedAsArgument($tokens, $argumentsAnalyzer, $variableNames, $arguments);

                    $index = $tokens->getNextTokenOfKind($functionCloseBraceIndex, ['{']);
                }

                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index); // skip body

                continue;
            }
        }

        return $variableNames;
    }

    /**
     * @param array<string, int> $variableNames variable name (including dollar) => index
     *
     * @return array<string, int>
     */
    private function filterVariablesUsedAsArgument(Tokens $tokens, ArgumentsAnalyzer $argumentsAnalyzer, array $variableNames, array $arguments)
    {
        foreach ($arguments as $start => $end) {
            $info = $argumentsAnalyzer->getArgumentInfo($tokens, $start, $end);
            $content = $info->getName();

            if (isset($variableNames[$content])) {
                unset($variableNames[$content]);

                if (0 === \count($variableNames)) {
                    return $variableNames;
                }
            }
        }

        return $variableNames;
    }

    private function getArgumentsAnalyzer()
    {
        if (null === $this->argumentsAnalyzer) {
            $this->argumentsAnalyzer = new ArgumentsAnalyzer();
        }

        return $this->argumentsAnalyzer;
    }

    private function getFunctionsAnalyzer()
    {
        if (null === $this->functionAnalyzer) {
            $this->functionAnalyzer = new FunctionsAnalyzer();
        }

        return $this->functionAnalyzer;
    }

    private function getTokensAnalyzer(Tokens $tokens)
    {
        return new TokensAnalyzer($tokens);
    }
}
