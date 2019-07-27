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
     * @param Tokens $tokens
     * @param int    $classIndex
     *
     * @return array Contains information about the class being analyzed
     */
    public function getClassDefinition(Tokens $tokens, $classIndex)
    {
        $openIndex = $tokens->getNextTokenOfKind($classIndex, ['{']);
        $prevIndex = $tokens->getPrevMeaningfulToken($classIndex);
        $startIndex = $tokens[$prevIndex]->isGivenKind([T_FINAL, T_ABSTRACT]) ? $prevIndex : $classIndex;

        $extends = false;
        $implements = false;
        $anonymousClass = false;

        if (!$tokens[$classIndex]->isGivenKind(T_TRAIT)) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classIndex, $openIndex);
            $extends = 0 !== \count($extends) ? $this->getClassInheritanceInfo($tokens, key($extends), 'numberOfExtends') : false;

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

    /**
     * @param Tokens $tokens
     * @param int    $classIndex
     *
     * @return null|TypeAnalysis
     *                           Get the class extends if any, null otherwise
     */
    public function getClassExtends(Tokens $tokens, $classIndex)
    {
        $definition = $this->getClassDefinition($tokens, $classIndex);

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
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param string $label
     *
     * @return array
     *               Contains information about the class inheritance
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
}
