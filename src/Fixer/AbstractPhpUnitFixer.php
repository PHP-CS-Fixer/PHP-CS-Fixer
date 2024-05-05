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

namespace PhpCsFixer\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
abstract class AbstractPhpUnitFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_STRING]);
    }

    final protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $phpUnitTestCaseIndicator = new PhpUnitTestCaseIndicator();

        foreach ($phpUnitTestCaseIndicator->findPhpUnitClasses($tokens) as $indices) {
            $this->applyPhpUnitClassFix($tokens, $indices[0], $indices[1]);
        }
    }

    abstract protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void;

    final protected function getDocBlockIndex(Tokens $tokens, int $index): int
    {
        $modifiers = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_COMMENT];

        if (\defined('T_ATTRIBUTE')) { // @TODO: drop condition when PHP 8.0+ is required
            $modifiers[] = T_ATTRIBUTE;
        }

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.2+ is required
            $modifiers[] = T_READONLY;
        }

        do {
            $index = $tokens->getPrevNonWhitespace($index);

            if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $index = $tokens->getPrevTokenOfKind($index, [[T_ATTRIBUTE]]);
            }
        } while ($tokens[$index]->isGivenKind($modifiers));

        return $index;
    }

    /**
     * @param list<string>       $preventingAnnotations
     * @param list<class-string> $preventingAttributes
     */
    final protected function ensureIsDocBlockWithAnnotation(
        Tokens $tokens,
        int $index,
        string $annotation,
        array $preventingAnnotations,
        array $preventingAttributes
    ): void {
        $docBlockIndex = $this->getDocBlockIndex($tokens, $index);

        if (self::isPreventedByAttribute($tokens, $index, $preventingAttributes)) {
            return;
        }

        if ($this->isPHPDoc($tokens, $docBlockIndex)) {
            $this->updateDocBlockIfNeeded($tokens, $docBlockIndex, $annotation, $preventingAnnotations);
        } else {
            $this->createDocBlock($tokens, $docBlockIndex, $annotation);
        }
    }

    final protected function isPHPDoc(Tokens $tokens, int $index): bool
    {
        return $tokens[$index]->isGivenKind(T_DOC_COMMENT);
    }

    private function createDocBlock(Tokens $tokens, int $docBlockIndex, string $annotation): void
    {
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $toInsert = [
            new Token([T_DOC_COMMENT, "/**{$lineEnd}{$originalIndent} * @{$annotation}{$lineEnd}{$originalIndent} */"]),
            new Token([T_WHITESPACE, $lineEnd.$originalIndent]),
        ];
        $index = $tokens->getNextMeaningfulToken($docBlockIndex);
        $tokens->insertAt($index, $toInsert);

        if (!$tokens[$index - 1]->isGivenKind(T_WHITESPACE)) {
            $extraNewLines = $this->whitespacesConfig->getLineEnding();

            if (!$tokens[$index - 1]->isGivenKind(T_OPEN_TAG)) {
                $extraNewLines .= $this->whitespacesConfig->getLineEnding();
            }

            $tokens->insertAt($index, [
                new Token([T_WHITESPACE, $extraNewLines.WhitespacesAnalyzer::detectIndent($tokens, $index)]),
            ]);
        }
    }

    /**
     * @param list<string> $preventingAnnotations
     */
    private function updateDocBlockIfNeeded(
        Tokens $tokens,
        int $docBlockIndex,
        string $annotation,
        array $preventingAnnotations
    ): void {
        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        foreach ($preventingAnnotations as $preventingAnnotation) {
            if ([] !== $doc->getAnnotationsOfType($preventingAnnotation)) {
                return;
            }
        }
        $doc = $this->makeDocBlockMultiLineIfNeeded($doc, $tokens, $docBlockIndex, $annotation);
        $lines = $this->addInternalAnnotation($doc, $tokens, $docBlockIndex, $annotation);
        $lines = implode('', $lines);

        $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $lines]);
    }

    /**
     * @param list<class-string> $preventingAttributes
     */
    private static function isPreventedByAttribute(Tokens $tokens, int $index, array $preventingAttributes): bool
    {
        if ([] === $preventingAttributes) {
            return false;
        }

        $attributeIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$attributeIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
            return false;
        }
        $attributeIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $attributeIndex);

        foreach (AttributeAnalyzer::collect($tokens, $attributeIndex) as $attributeAnalysis) {
            foreach ($attributeAnalysis->getAttributes() as $attribute) {
                if (\in_array(self::getFullyQualifiedName($tokens, $attribute['name']), $preventingAttributes, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function getFullyQualifiedName(Tokens $tokens, string $name): string
    {
        $name = strtolower($name);

        $names = [];
        foreach ((new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens) as $namespaceUseAnalysis) {
            $names[strtolower($namespaceUseAnalysis->getShortName())] = strtolower($namespaceUseAnalysis->getFullName());
        }

        foreach ($names as $shortName => $fullName) {
            if ($name === $shortName) {
                return $fullName;
            }

            if (!str_starts_with($name, $shortName.'\\')) {
                continue;
            }

            return $fullName.substr($name, \strlen($shortName));
        }

        return $name;
    }

    /**
     * @return list<Line>
     */
    private function addInternalAnnotation(DocBlock $docBlock, Tokens $tokens, int $docBlockIndex, string $annotation): array
    {
        $lines = $docBlock->getLines();
        $originalIndent = WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        array_splice($lines, -1, 0, $originalIndent.' * @'.$annotation.$lineEnd);

        return $lines;
    }

    private function makeDocBlockMultiLineIfNeeded(DocBlock $doc, Tokens $tokens, int $docBlockIndex, string $annotation): DocBlock
    {
        $lines = $doc->getLines();
        if (1 === \count($lines) && [] === $doc->getAnnotationsOfType($annotation)) {
            $indent = WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
            $doc->makeMultiLine($indent, $this->whitespacesConfig->getLineEnding());

            return $doc;
        }

        return $doc;
    }
}
