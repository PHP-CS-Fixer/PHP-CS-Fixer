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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class DisjunctiveNormalFormTypeBracesTransformer extends AbstractTransformer
{
    /**
     * @var list<int>
     */
    private array $propertyModifierTypes = []; // @TODO: replace with `const` when PHP 8.1+ is required

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [
            CT::T_DNF_TYPE_PARENTHESIS_OPEN,
            CT::T_DNF_TYPE_PARENTHESIS_CLOSE,
        ];
    }

    public function getPriority(): int
    {
        return -12;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 80200;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        $this->initializeModifiersTypes();

        if ($tokens[$index]->isGivenKind(CT::T_TYPE_COLON)) {
            $this->fixBracesOfReturnTypeCandidate($tokens, $index);

            return;
        }

        if ($tokens[$index]->isGivenKind($this->propertyModifierTypes)) {
            $this->fixBracesOfPropertyCandidate($tokens, $index);

            return;
        }

        if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
            $functionBraceOpenIndex = $tokens->getNextTokenOfKind($index, ['(']);

            $this->fixBracesInFunctionArgumentsCandidate(
                $tokens,
                $tokens->getNextMeaningfulToken($functionBraceOpenIndex),
                $this->findBraceCloseIndex($tokens, $functionBraceOpenIndex)
            );

            // implicit return
        }
    }

    private function fixBracesOfReturnTypeCandidate(Tokens $tokens, int $index): void
    {
        $index = $tokens->getNextMeaningfulToken($index);

        if ($this->isPossibleStartOfDnfType($tokens, $index)) {
            $this->fixBracesOfDnfTypeCandidate($tokens, $index);
        }
    }

    private function fixBracesOfPropertyCandidate(Tokens $tokens, int $index): void
    {
        if ($tokens[$index]->isGivenKind([T_STATIC])) {
            $this->fixBracesOfStaticPropertyCandidate($tokens, $index);

            return;
        }

        do {
            $index = $tokens->getNextMeaningfulToken($index);
        } while ($tokens[$index]->isGivenKind($this->propertyModifierTypes));

        if ($this->isPossibleStartOfDnfType($tokens, $index)) {
            $this->fixBracesOfDnfTypeCandidate($tokens, $index);
        }
    }

    private function fixBracesOfStaticPropertyCandidate(Tokens $tokens, int $index): void
    {
        $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($index);

        if ($tokens[$nextMeaningfulIndex]->isGivenKind([T_READONLY])) {
            $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($index);
        }

        if (!$this->isPossibleStartOfDnfType($tokens, $nextMeaningfulIndex)) {
            return;
        }

        $prev = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$prev]->equalsAny(['{', ';', [CT::T_ATTRIBUTE_CLOSE]])) { // FIXME utests
            return;
        }

        $this->fixBracesOfDnfTypeCandidate($tokens, $nextMeaningfulIndex);
    }

    private function fixBracesInFunctionArgumentsCandidate(Tokens $tokens, int $index, int $endIndex): void
    {
        while (true) {
            if ($this->isPossibleStartOfDnfType($tokens, $index)) {
                $this->fixBracesOfDnfTypeCandidate($tokens, $index);
            }

            $index = $tokens->getNextTokenOfKind($index, [',', [T_ARRAY], [CT::T_ARRAY_SQUARE_BRACE_OPEN]]);

            if (null === $index || $index >= $endIndex) {
                break;
            }

            if ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                $index = $tokens->getNextMeaningfulToken($index);
            } elseif ($tokens[$index]->isGivenKind(T_ARRAY)) {
                $index = $tokens->getNextMeaningfulToken($index);
                $index = $this->findBraceCloseIndex($tokens, $index);
                $index = $tokens->getNextMeaningfulToken($index);
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    private function fixBracesOfDnfTypeCandidate(Tokens $tokens, int $index): void
    {
        if ($tokens[$index]->equals('(')) {
            $closeIndex = $this->findBraceCloseIndex($tokens, $index);
            $this->replaceBraces($tokens, $index, $closeIndex);
            $index = $tokens->getNextMeaningfulToken($closeIndex);
        } elseif ($tokens[$index]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            do {
                $index = $tokens->getNextMeaningfulToken($index);
            } while ($tokens[$index]->isGivenKind([T_NS_SEPARATOR, T_STRING]));
        } else {
            return;
        }

        if ($tokens[$index]->equals('|')) {
            $this->fixBracesOfDnfTypeCandidate($tokens, $tokens->getNextMeaningfulToken($index));
        }
    }

    private function replaceBraces(Tokens $tokens, int $openBraceIndex, int $closeBraceIndex): void
    {
        $tokens[$openBraceIndex] = new Token([CT::T_DNF_TYPE_PARENTHESIS_OPEN, '(']);
        $tokens[$closeBraceIndex] = new Token([CT::T_DNF_TYPE_PARENTHESIS_CLOSE, ')']);
    }

    private function findBraceCloseIndex(Tokens $tokens, int $openBraceIndex): int
    {
        $closeBraceIndex = $openBraceIndex;
        $depth = 1;

        do {
            $closeBraceIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);

            if ($tokens[$closeBraceIndex]->equals(')')) {
                --$depth;
            } elseif ($tokens[$closeBraceIndex]->equals('(')) {
                ++$depth;
            }
        } while (0 !== $depth);

        return $closeBraceIndex;
    }

    private function isPossibleStartOfDnfType(Tokens $tokens, int $index): bool
    {
        return $tokens[$index]->equalsAny(['(', [T_NS_SEPARATOR], [T_STRING]]);
    }

    private function initializeModifiersTypes(): void
    {
        if (0 === \count($this->propertyModifierTypes)) {
            $this->propertyModifierTypes = [
                T_PRIVATE,
                T_PROTECTED,
                T_PUBLIC,
                T_READONLY,
                T_STATIC,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
            ];
        }
    }
}
