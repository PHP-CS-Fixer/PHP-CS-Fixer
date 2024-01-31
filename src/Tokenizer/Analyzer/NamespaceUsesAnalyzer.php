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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
final class NamespaceUsesAnalyzer
{
    /**
     * @return list<NamespaceUseAnalysis>
     */
    public function getDeclarationsFromTokens(Tokens $tokens, bool $allowMultiUses = false): array
    {
        $tokenAnalyzer = new TokensAnalyzer($tokens);
        $useIndices = $tokenAnalyzer->getImportUseIndexes();

        return $this->getDeclarations($tokens, $useIndices, $allowMultiUses);
    }

    /**
     * @return list<NamespaceUseAnalysis>
     */
    public function getDeclarationsInNamespace(Tokens $tokens, NamespaceAnalysis $namespace, bool $allowMultiUses = false): array
    {
        $namespaceUses = [];

        foreach ($this->getDeclarationsFromTokens($tokens, $allowMultiUses) as $namespaceUse) {
            if ($namespaceUse->getStartIndex() >= $namespace->getScopeStartIndex() && $namespaceUse->getStartIndex() <= $namespace->getScopeEndIndex()) {
                $namespaceUses[] = $namespaceUse;
            }
        }

        return $namespaceUses;
    }

    /**
     * @param list<int> $useIndices
     *
     * @return list<NamespaceUseAnalysis>
     */
    private function getDeclarations(Tokens $tokens, array $useIndices, bool $allowMultiUses = false): array
    {
        $uses = [];

        foreach ($useIndices as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);

            // TODO Collect all uses, then filter out multi-use imports if not requested
            $analysis = true === $allowMultiUses
                ? $this->parseAllDeclarations($index, $endIndex, $tokens)
                : array_filter([$this->parseSingleDeclaration($index, $endIndex, $tokens)]);

            if ([] !== $analysis) {
                $uses = array_merge($uses, $analysis);
            }
        }

        return $uses;
    }

    /**
     * @return list<NamespaceUseAnalysis>
     */
    private function parseAllDeclarations(int $startIndex, int $endIndex, Tokens $tokens): array
    {
        throw new \RuntimeException('Not implemented');
    }

    private function parseSingleDeclaration(int $startIndex, int $endIndex, Tokens $tokens): ?NamespaceUseAnalysis
    {
        $fullName = $shortName = '';
        $aliased = false;

        $type = NamespaceUseAnalysis::TYPE_CLASS;
        for ($i = $startIndex; $i <= $endIndex; ++$i) {
            $token = $tokens[$i];
            if ($token->equals(',') || $token->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                // do not touch group use declarations until the logic of this is added (for example: `use some\a\{ClassD};`)
                // ignore multiple use statements that should be split into few separate statements (for example: `use BarB, BarC as C;`)
                return null;
            }

            if ($token->isGivenKind(CT::T_FUNCTION_IMPORT)) {
                $type = NamespaceUseAnalysis::TYPE_FUNCTION;
            } elseif ($token->isGivenKind(CT::T_CONST_IMPORT)) {
                $type = NamespaceUseAnalysis::TYPE_CONSTANT;
            }

            if ($token->isWhitespace() || $token->isComment() || $token->isGivenKind(T_USE)) {
                continue;
            }

            if ($token->isGivenKind(T_STRING)) {
                $shortName = $token->getContent();
                if (!$aliased) {
                    $fullName .= $shortName;
                }
            } elseif ($token->isGivenKind(T_NS_SEPARATOR)) {
                $fullName .= $token->getContent();
            } elseif ($token->isGivenKind(T_AS)) {
                $aliased = true;
            }
        }

        return new NamespaceUseAnalysis(
            $type,
            trim($fullName),
            $shortName,
            $aliased,
            false,
            $startIndex,
            $endIndex,
        );
    }
}
