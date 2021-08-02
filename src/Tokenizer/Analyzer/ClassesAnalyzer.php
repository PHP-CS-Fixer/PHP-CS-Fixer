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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
final class ClassesAnalyzer
{
    /**
     * @param int $classIndex
     *
     * @return ClassAnalysis
     */
    public function getClassDefinition(Tokens $tokens, $classIndex)
    {
        $openIndex = $tokens->getNextTokenOfKind($classIndex, ['{']);
        $prevIndex = $tokens->getPrevMeaningfulToken($classIndex);
        $startIndex = $tokens[$prevIndex]->isGivenKind([T_FINAL, T_ABSTRACT]) ? $prevIndex : $classIndex;

        $extends = [];
        $implements = [];
        $anonymousClass = false;

        if (!$tokens[$classIndex]->isGivenKind(T_TRAIT)) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classIndex, $openIndex);
            $extends = [] !== $extends ? $this->getClassInheritanceInfo($tokens, key($extends), 'numberOfExtends') : [];

            if (!$tokens[$classIndex]->isGivenKind(T_INTERFACE)) {
                $implements = $tokens->findGivenKind(T_IMPLEMENTS, $classIndex, $openIndex);
                $implements = [] !== $implements ? $this->getClassInheritanceInfo($tokens, key($implements), 'numberOfImplements') : [];
                $tokensAnalyzer = new TokensAnalyzer($tokens);
                $anonymousClass = $tokensAnalyzer->isAnonymousClass($classIndex);
            }
        }

        return new ClassAnalysis($startIndex, $classIndex, $openIndex, $extends, $implements, $anonymousClass);
    }

    /**
     * @param int $classIndex
     *
     * @return null|TypeAnalysis Get the class extends if any, null otherwise
     */
    public function getClassExtends(Tokens $tokens, $classIndex)
    {
        $analysis = $this->getClassDefinition($tokens, $classIndex);

        if ([] === $analysis->getExtends()) {
            return null;
        }

        $typeStartIndex = $tokens->getNextNonWhitespace(
            $analysis->getExtends()['start']
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
     * @param int    $startIndex
     * @param string $label      an arbitrary name that will be used as key property
     *                           in the return array
     *
     * @return array contains information about the class inheritance, keyed by
     *               the $label parameter
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
