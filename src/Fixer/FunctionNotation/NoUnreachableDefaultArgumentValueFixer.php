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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Mark Scherer
 * @author Lucas Manzke <lmanzke@outlook.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NoUnreachableDefaultArgumentValueFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'In function arguments there must not be arguments with default values before non-default ones.',
            [
                new CodeSample(
                    '<?php
function example($foo = "two words", $bar) {}
'
                ),
            ],
            null,
            'Modifies the signature of functions; therefore risky when using systems (such as some Symfony components) that rely on those (for example through reflection).'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after NullableTypeDeclarationForDefaultNullValueFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_FUNCTION, T_FN]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionKinds = [T_FUNCTION, T_FN];

        for ($i = 0, $l = $tokens->count(); $i < $l; ++$i) {
            if (!$tokens[$i]->isGivenKind($functionKinds)) {
                continue;
            }

            $startIndex = $tokens->getNextTokenOfKind($i, ['(']);
            $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);

            $this->fixFunctionDefinition($tokens, $startIndex, $i);
        }
    }

    private function fixFunctionDefinition(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $lastArgumentIndex = $this->getLastNonDefaultArgumentIndex($tokens, $startIndex, $endIndex);

        if (null === $lastArgumentIndex) {
            return;
        }

        for ($i = $lastArgumentIndex; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_VARIABLE)) {
                $lastArgumentIndex = $i;

                continue;
            }

            if (!$token->equals('=') || $this->isNonNullableTypehintedNullableVariable($tokens, $i)) {
                continue;
            }

            $this->removeDefaultValue($tokens, $i, $this->getDefaultValueEndIndex($tokens, $lastArgumentIndex));
        }
    }

    private function getLastNonDefaultArgumentIndex(Tokens $tokens, int $startIndex, int $endIndex): ?int
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->equals('=')) {
                $i = $tokens->getPrevMeaningfulToken($i);

                continue;
            }

            if ($token->isGivenKind(T_VARIABLE) && !$this->isEllipsis($tokens, $i)) {
                return $i;
            }
        }

        return null;
    }

    private function isEllipsis(Tokens $tokens, int $variableIndex): bool
    {
        return $tokens[$tokens->getPrevMeaningfulToken($variableIndex)]->isGivenKind(T_ELLIPSIS);
    }

    private function getDefaultValueEndIndex(Tokens $tokens, int $index): int
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
            }
        } while (!$tokens[$index]->equals(','));

        return $tokens->getPrevMeaningfulToken($index);
    }

    private function removeDefaultValue(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        for ($i = $startIndex; $i <= $endIndex;) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
            $this->clearWhitespacesBeforeIndex($tokens, $i);
            $i = $tokens->getNextMeaningfulToken($i);
        }
    }

    /**
     * @param int $index Index of "="
     */
    private function isNonNullableTypehintedNullableVariable(Tokens $tokens, int $index): bool
    {
        $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if (!$nextToken->equals([T_STRING, 'null'], false)) {
            return false;
        }

        $variableIndex = $tokens->getPrevMeaningfulToken($index);

        $searchTokens = [',', '(', [T_STRING], [CT::T_ARRAY_TYPEHINT], [T_CALLABLE]];
        $typehintKinds = [T_STRING, CT::T_ARRAY_TYPEHINT, T_CALLABLE];

        $prevIndex = $tokens->getPrevTokenOfKind($variableIndex, $searchTokens);

        if (!$tokens[$prevIndex]->isGivenKind($typehintKinds)) {
            return false;
        }

        return !$tokens[$tokens->getPrevMeaningfulToken($prevIndex)]->isGivenKind(CT::T_NULLABLE_TYPE);
    }

    private function clearWhitespacesBeforeIndex(Tokens $tokens, int $index): void
    {
        $prevIndex = $tokens->getNonEmptySibling($index, -1);
        if (!$tokens[$prevIndex]->isWhitespace()) {
            return;
        }

        $prevNonWhiteIndex = $tokens->getPrevNonWhitespace($prevIndex);
        if (null === $prevNonWhiteIndex || !$tokens[$prevNonWhiteIndex]->isComment()) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($prevIndex);
        }
    }
}
