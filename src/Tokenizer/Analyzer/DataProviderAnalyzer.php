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

use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DataProviderAnalysis;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class DataProviderAnalyzer
{
    private const REGEX_CLASS = '(?:\\\\?+'.TypeExpression::REGEX_IDENTIFIER
        .'(\\\\'.TypeExpression::REGEX_IDENTIFIER.')*+)';

    /**
     * @return array<DataProviderAnalysis>
     */
    public function getDataProviders(Tokens $tokens, int $startIndex, int $endIndex): array
    {
        $methods = $this->getMethods($tokens, $startIndex, $endIndex);

        $dataProviders = [];
        foreach ($methods as $methodIndex) {
            $docCommentIndex = $tokens->getTokenNotOfKindSibling(
                $methodIndex,
                -1,
                [[T_ABSTRACT], [T_COMMENT], [T_FINAL], [T_FUNCTION], [T_PRIVATE], [T_PROTECTED], [T_PUBLIC], [T_STATIC], [T_WHITESPACE]]
            );

            if (!$tokens[$docCommentIndex]->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            Preg::matchAll('/@dataProvider\h+(('.self::REGEX_CLASS.'::)?'.TypeExpression::REGEX_IDENTIFIER.')/', $tokens[$docCommentIndex]->getContent(), $matches);

            /** @var array<string> $matches */
            $matches = $matches[1];

            foreach ($matches as $dataProviderName) {
                $dataProviders[$dataProviderName][] = $docCommentIndex;
            }
        }

        $dataProviderAnalyses = [];
        foreach ($dataProviders as $dataProviderName => $dataProviderUsages) {
            $lowercaseDataProviderName = strtolower($dataProviderName);
            if (!\array_key_exists($lowercaseDataProviderName, $methods)) {
                continue;
            }
            $dataProviderAnalyses[$methods[$lowercaseDataProviderName]] = new DataProviderAnalysis(
                $tokens[$methods[$lowercaseDataProviderName]]->getContent(),
                $methods[$lowercaseDataProviderName],
                $dataProviderUsages,
            );
        }

        ksort($dataProviderAnalyses);

        return array_values($dataProviderAnalyses);
    }

    /**
     * @return array<string, int>
     */
    private function getMethods(Tokens $tokens, int $startIndex, int $endIndex): array
    {
        $functions = [];
        for ($index = $startIndex; $index < $endIndex; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $functionNameIndex = $tokens->getNextNonWhitespace($index);

            if (!$tokens[$functionNameIndex]->isGivenKind(T_STRING)) {
                continue;
            }

            $functions[strtolower($tokens[$functionNameIndex]->getContent())] = $functionNameIndex;
        }

        return $functions;
    }
}
