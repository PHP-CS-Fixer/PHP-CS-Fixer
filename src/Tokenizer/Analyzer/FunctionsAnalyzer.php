<?php

declare(strict_types=1);

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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class FunctionsAnalyzer
{
    /**
     * @var array{tokens: string, imports: list<NamespaceUseAnalysis>, declarations: list<int>}
     */
    private array $functionsAnalysis = ['tokens' => '', 'imports' => [], 'declarations' => []];

    /**
     * Important: risky because of the limited (file) scope of the tool.
     */
    public function isGlobalFunctionCall(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return false;
        }

        $nextIndex = $tokens->getNextMeaningfulToken($index);

        if (!$tokens[$nextIndex]->equals('(')) {
            return false;
        }

        $previousIsNamespaceSeparator = false;
        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
            $previousIsNamespaceSeparator = true;
            $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
        }

        $possibleKind = array_merge([T_DOUBLE_COLON, T_FUNCTION, CT::T_NAMESPACE_OPERATOR, T_NEW, CT::T_RETURN_REF, T_STRING], Token::getObjectOperatorKinds());

        // @TODO: drop condition when PHP 8.0+ is required
        if (\defined('T_ATTRIBUTE')) {
            $possibleKind[] = T_ATTRIBUTE;
        }

        if ($tokens[$prevIndex]->isGivenKind($possibleKind)) {
            return false;
        }

        if ($tokens[$tokens->getNextMeaningfulToken($nextIndex)]->isGivenKind(CT::T_FIRST_CLASS_CALLABLE)) {
            return false;
        }

        if ($previousIsNamespaceSeparator) {
            return true;
        }

        if ($tokens->isChanged() || $tokens->getCodeHash() !== $this->functionsAnalysis['tokens']) {
            $this->buildFunctionsAnalysis($tokens);
        }

        // figure out in which namespace we are
        $scopeStartIndex = 0;
        $scopeEndIndex = \count($tokens) - 1;
        $inGlobalNamespace = false;

        foreach ($tokens->getNamespaceDeclarations() as $declaration) {
            $scopeStartIndex = $declaration->getScopeStartIndex();
            $scopeEndIndex = $declaration->getScopeEndIndex();

            if ($index >= $scopeStartIndex && $index <= $scopeEndIndex) {
                $inGlobalNamespace = $declaration->isGlobalNamespace();

                break;
            }
        }

        $call = strtolower($tokens[$index]->getContent());

        // check if the call is to a function declared in the same namespace as the call is done,
        // if the call is already in the global namespace than declared functions are in the same
        // global namespace and don't need checking

        if (!$inGlobalNamespace) {
            /** @var int $functionNameIndex */
            foreach ($this->functionsAnalysis['declarations'] as $functionNameIndex) {
                if ($functionNameIndex < $scopeStartIndex || $functionNameIndex > $scopeEndIndex) {
                    continue;
                }

                if (strtolower($tokens[$functionNameIndex]->getContent()) === $call) {
                    return false;
                }
            }
        }

        /** @var NamespaceUseAnalysis $functionUse */
        foreach ($this->functionsAnalysis['imports'] as $functionUse) {
            if ($functionUse->getStartIndex() < $scopeStartIndex || $functionUse->getEndIndex() > $scopeEndIndex) {
                continue;
            }

            if ($call !== strtolower($functionUse->getShortName())) {
                continue;
            }

            // global import like `use function \str_repeat;`
            return $functionUse->getShortName() === ltrim($functionUse->getFullName(), '\\');
        }

        if (AttributeAnalyzer::isAttribute($tokens, $index)) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, ArgumentAnalysis>
     */
    public function getFunctionArguments(Tokens $tokens, int $functionIndex): array
    {
        $argumentsStart = $tokens->getNextTokenOfKind($functionIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $argumentAnalyzer = new ArgumentsAnalyzer();
        $arguments = [];

        foreach ($argumentAnalyzer->getArguments($tokens, $argumentsStart, $argumentsEnd) as $start => $end) {
            $argumentInfo = $argumentAnalyzer->getArgumentInfo($tokens, $start, $end);
            $arguments[$argumentInfo->getName()] = $argumentInfo;
        }

        return $arguments;
    }

    public function getFunctionReturnType(Tokens $tokens, int $methodIndex): ?TypeAnalysis
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $typeColonIndex = $tokens->getNextMeaningfulToken($argumentsEnd);

        if (!$tokens[$typeColonIndex]->isGivenKind(CT::T_TYPE_COLON)) {
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

    public function isTheSameClassCall(Tokens $tokens, int $index): bool
    {
        if (!$tokens->offsetExists($index)) {
            return false;
        }

        $operatorIndex = $tokens->getPrevMeaningfulToken($index);

        if (null === $operatorIndex) {
            return false;
        }

        if (!$tokens[$operatorIndex]->isObjectOperator() && !$tokens[$operatorIndex]->isGivenKind(T_DOUBLE_COLON)) {
            return false;
        }

        $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);

        if (null === $referenceIndex) {
            return false;
        }

        return $tokens[$referenceIndex]->equalsAny([[T_VARIABLE, '$this'], [T_STRING, 'self'], [T_STATIC, 'static']], false);
    }

    private function buildFunctionsAnalysis(Tokens $tokens): void
    {
        $this->functionsAnalysis = [
            'tokens' => $tokens->getCodeHash(),
            'imports' => [],
            'declarations' => [],
        ];

        // find declarations

        if ($tokens->isTokenKindFound(T_FUNCTION)) {
            $end = \count($tokens);

            for ($i = 0; $i < $end; ++$i) {
                // skip classy, we are looking for functions not methods
                if ($tokens[$i]->isGivenKind(Token::getClassyTokenKinds())) {
                    $i = $tokens->getNextTokenOfKind($i, ['(', '{']);

                    if ($tokens[$i]->equals('(')) { // anonymous class
                        $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);
                        $i = $tokens->getNextTokenOfKind($i, ['{']);
                    }

                    $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);

                    continue;
                }

                if (!$tokens[$i]->isGivenKind(T_FUNCTION)) {
                    continue;
                }

                $i = $tokens->getNextMeaningfulToken($i);

                if ($tokens[$i]->isGivenKind(CT::T_RETURN_REF)) {
                    $i = $tokens->getNextMeaningfulToken($i);
                }

                if (!$tokens[$i]->isGivenKind(T_STRING)) {
                    continue;
                }

                $this->functionsAnalysis['declarations'][] = $i;
            }
        }

        // find imported functions

        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();

        if ($tokens->isTokenKindFound(CT::T_FUNCTION_IMPORT)) {
            $declarations = $namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);

            foreach ($declarations as $declaration) {
                if ($declaration->isFunction()) {
                    $this->functionsAnalysis['imports'][] = $declaration;
                }
            }
        }
    }
}
