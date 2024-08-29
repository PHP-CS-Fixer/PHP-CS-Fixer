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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Philippe Bouttereux <philippe.bouttereux@gmail.com>
 */
final class MultilineLongArrayFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private int $maxArrayLen = 0;

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->maxArrayLen = $this->configuration['max_length'] ?? 0;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'A single-line array should be broken into multiple lines if it exceeds configured limit. Arrays that contain comments should be left unchanged.',
            [
                new CodeSample("<?php\n\$array = ['a very very long element','another very long element'];\n"),
                new CodeSample("<?php\n\$array = ['a very very long element','another very long element'];\n", ['max_length' => 10]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer, NoTrailingCommaInSinglelineFixer, TrailingCommaInMultilineFixer, WhitespaceAfterCommaInArrayFixer.
     */
    public function getPriority(): int
    {
        return 50;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            T_ARRAY,
            CT::T_ARRAY_SQUARE_BRACE_OPEN,
            CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
        ]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('max_length', 'Maximum length in characters (excluding whitespaces) for single-line arrays. 0 : multi-line only, -1 : single-line only.'))
                ->setAllowedTypes(['int'])
                ->setDefault(0)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensToInsert = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind([
                T_ARRAY,
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
            ])) {
                continue;
            }

            if ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $startIndex = $index;
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
            } elseif ($tokens[$index]->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN)) {
                $startIndex = $index;
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DESTRUCTURING_SQUARE_BRACE, $startIndex);
            } else {
                $startIndex = $tokens->getNextTokenOfKind($index, ['(']);
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
            }

            if ($this->maxArrayLen > -1) {
                $shouldBeMultiline = $this->computeArrayLen($tokens, $startIndex, $endIndex) > $this->maxArrayLen;
            } else {
                $shouldBeMultiline = false;
            }

            // Users should be free to insert comments as they wish.
            if ($this->arrayContainsComments($tokens, $startIndex, $endIndex)) {
                continue;
            }

            $token = $this->handleLineEnding($startIndex, $tokens, $shouldBeMultiline);
            if (null !== $token) {
                $tokensToInsert[$startIndex + 1] = $token;
            }

            for ($i = $endIndex - 1; $i > $startIndex; --$i) {
                $i = $this->skipNestedStructures($i, $tokens);
                if (!$tokens[$i]->equals(',')) {
                    continue;
                }

                $token = $this->handleLineEnding($i, $tokens, $shouldBeMultiline);
                if (null !== $token) {
                    $tokensToInsert[$i + 1] = $token;
                }
            }

            $prevToken = $tokens->getPrevMeaningfulToken($endIndex);
            $newToken = $this->handleLineEnding($prevToken, $tokens, $shouldBeMultiline);
            if (null !== $newToken) {
                $tokensToInsert[$prevToken + 1] = $newToken;
            }
        }

        $tokens->insertSlices($tokensToInsert);
    }

    private function arrayContainsComments(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $i = $this->skipNestedStructures($i, $tokens);

            if ($tokens[$i]->isComment()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int the length of the array excluding brackets and whitespaces
     */
    private function computeArrayLen(Tokens $tokens, int $startIndex, int $endIndex): int
    {
        $total = 0;

        for ($i = $startIndex + 1; $i < $endIndex; ++$i) {
            if (!$tokens[$i]->isWhitespace()) {
                $total += \strlen($tokens[$i]->getContent());
            }
        }

        return $total;
    }

    /**
     * Depending on whether the array is longer than the `max_length` argument or not, adding or removing a line ending.
     *
     * @return null|Token if not null, a new token to insert at position $index + 1
     */
    private function handleLineEnding(int $index, Tokens &$tokens, bool $shouldBeMultiline): ?Token
    {
        $isTokenALineEnding = str_contains($tokens[$index + 1]->getContent(), "\n");

        if ($shouldBeMultiline) {
            if ($tokens[$index + 1]->isWhitespace(" \t\0\x0B")) {
                $tokens[$index + 1] = new Token([T_WHITESPACE, "\n"]);

                return null;
            }

            if (!$isTokenALineEnding) {
                return new Token([T_WHITESPACE, "\n"]);
            }
        } else {
            if ($isTokenALineEnding) {
                $tokens->clearAt($index + 1);
            }
        }

        return null;
    }

    /**
     * Moves the index over nested arrays and non-array structures like callbacks.
     *
     * @return int New index
     */
    private function skipNestedStructures(int $index, Tokens $tokens): int
    {
        if ($tokens[$index]->equals('}')) {
            return $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        if ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
        }

        if ($tokens[$index]->equals(')')) {
            $startIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            return $tokens->getPrevMeaningfulToken($startIndex);
        }

        if ($tokens[$index]->equals(',') && $this->commaIsPartOfImplementsList($index, $tokens)) {
            --$index;
        }

        return $index;
    }

    private function commaIsPartOfImplementsList(int $index, Tokens $tokens): bool
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);

            $current = $tokens[$index];
        } while ($current->isGivenKind(T_STRING) || $current->equals(','));

        return $current->isGivenKind(T_IMPLEMENTS);
    }
}
