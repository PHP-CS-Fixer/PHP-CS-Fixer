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
    private const REGEX_CLASS = '(?:\\\?+'.TypeExpression::REGEX_IDENTIFIER
        .'(\\\\'.TypeExpression::REGEX_IDENTIFIER.')*+)';

    /**
     * @return list<DataProviderAnalysis>
     */
    public function getDataProviders(Tokens $tokens, int $startIndex, int $endIndex): array
    {
        $methods = $this->getMethods($tokens, $startIndex, $endIndex);

        $dataProviders = [];
        foreach ($methods as $methodIndex) {
            $docCommentIndex = $this->getDocCommentIndex($tokens, $methodIndex);

            if (null !== $docCommentIndex) {
                Preg::matchAll(
                    '/@dataProvider\h+(('.self::REGEX_CLASS.'::)?'.TypeExpression::REGEX_IDENTIFIER.')/',
                    $tokens[$docCommentIndex]->getContent(),
                    $matches,
                    PREG_OFFSET_CAPTURE
                );

                foreach ($matches[1] as $k => [$matchName]) {
                    \assert(isset($matches[0][$k]));

                    $dataProviders[$matchName][] = [$docCommentIndex, $matches[0][$k][1]];
                }
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

    private function getDocCommentIndex(Tokens $tokens, int $index): ?int
    {
        $docCommentIndex = null;
        while (!$tokens[$index]->equalsAny([';', '{', '}', [T_OPEN_TAG]])) {
            --$index;

            if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
                $docCommentIndex = $index;

                break;
            }
        }

        return $docCommentIndex;
    }
}
