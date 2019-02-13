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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
final class ClassAnalyzer
{
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param $classIndex
     *
     * @return array
     */
    public function getClassDefinition(Tokens $tokens, $classIndex)
    {
        return $this->getClassDefinitionType($tokens, $classIndex);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param $classIndex
     *
     * @return null|\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis
     */
    public function getClassExtends(Tokens $tokens, $classIndex)
    {
        $definition = $this->getClassDefinitionType($tokens, $classIndex);

        if (!isset($definition['extends']['start'])) {
            return null;
        }

        $typeStartIndex = $tokens->getNextNonWhitespace(
            $definition['extends']['start']
        );

        $type = $tokens[$typeStartIndex]->getContent();
        $countTokens = \count($tokens);

        for ($typeEndIndex = $typeStartIndex; $typeEndIndex < $countTokens; ++$typeEndIndex) {
            if ($tokens[$typeEndIndex + 1]->isWhitespace()) {
                break;
            }

            $type .= $tokens[$typeEndIndex + 1]->getContent();
        }

        return new TypeAnalysis($type, $typeStartIndex, $typeEndIndex);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param $startIndex
     * @param $label
     *
     * @return array
     */
    public function getClassInheritanceInfo(Tokens $tokens, $startIndex, $label)
    {
        $implementsInfo = ['start' => $startIndex, $label => 1, 'multiLine' => false];
        ++$startIndex;
        $endIndex = $tokens->getNextTokenOfKind($startIndex, ['{', [T_IMPLEMENTS], [T_EXTENDS]]);
        $endIndex = $tokens[$endIndex]->equals('{') ? $tokens->getPrevNonWhitespace($endIndex) : $endIndex;
        for ($i = $startIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->equals(',')) {
                ++$implementsInfo[$label];

                continue;
            }

            if (!$implementsInfo['multiLine'] && false !== strpos($tokens[$i]->getContent(), "\n")) {
                $implementsInfo['multiLine'] = true;
            }
        }

        return $implementsInfo;
    }

    /**
     * @param $tokens
     * @param $classIndex
     *
     * @return array
     */
    private function getClassDefinitionType(Tokens $tokens, $classIndex)
    {
        $openIndex = $tokens->getNextTokenOfKind($classIndex, ['{']);
        $prev = $tokens->getPrevMeaningfulToken($classIndex);
        $startIndex = $tokens[$prev]->isGivenKind([T_FINAL, T_ABSTRACT]) ? $prev : $classIndex;

        $extends = false;
        $implements = false;
        $anonymousClass = false;

        if (!$tokens[$classIndex]->isGivenKind(T_TRAIT)) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classIndex, $openIndex);
            $extends = \count($extends) ? $this->getClassInheritanceInfo($tokens, key($extends), 'numberOfExtends') : false;

            if (!$tokens[$classIndex]->isGivenKind(T_INTERFACE)) {
                $implements = $tokens->findGivenKind(T_IMPLEMENTS, $classIndex, $openIndex);
                $implements = \count($implements) ? $this->getClassInheritanceInfo($tokens, key($implements), 'numberOfImplements') : false;
                $tokensAnalyzer = new TokensAnalyzer($tokens);
                $anonymousClass = $tokensAnalyzer->isAnonymousClass($classIndex);
            }
        }

        return [
            'start' => $startIndex,
            'classy' => $classIndex,
            'open' => $openIndex,
            'extends' => $extends,
            'implements' => $implements,
            'anonymousClass' => $anonymousClass,
        ];
    }
}
