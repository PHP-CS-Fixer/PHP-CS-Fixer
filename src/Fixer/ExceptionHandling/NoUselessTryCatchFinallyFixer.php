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

namespace PhpCsFixer\Fixer\ExceptionHandling;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class NoUselessTryCatchFinallyFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Exceptions should not be caught to only be thrown. A `finally` statement must not be empty.',
            [
                new CodeSample(
                    "<?php\ntry {\n    foo();\n} catch(\\Exception \$e) {\n    throw \$e;\n}\n"
                ),
                new CodeSample(
                    "<?php\ntry {\n    foo();\n} catch(\\Exception \$e) {\n    echo 1;\n}\nfinally {}\n"
                ),
            ],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_TRY);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoTrailingWhitespaceFixer, NoWhitespaceInBlankLineFixer.
     * Must run after NoEmptyStatementFixer, NoUnneededCurlyBracesFixer.
     */
    public function getPriority(): int
    {
        return 9;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getTryCatchFinallyAnalysis($tokens) as $analysis) {
            $this->fixTryCatchFinally($tokens, $analysis);
        }
    }

    /**
     * @param array{
     *     try: array{
     *         index: int,
     *         block_open: int,
     *         block_close: int,
     *     },
     *     catch: array{
     *         all_catch_only_throws: bool,
     *         blocks: list<array{
     *             only_catch_throws: bool,
     *             index?: int,
     *             open?: int,
     *             close?: int,
     *             block_open?: int,
     *             throw?: int,
     *             throw_var?: int,
     *             throw_semicolon?: int,
     *             block_close: int,
     *         }>,
     *     },
     *     finally?: array{
     *         empty: bool,
     *         index: int,
     *         block_open: int,
     *         block_close: int|null,
     *     },
     * } $analysis
     */
    private function fixTryCatchFinally(Tokens $tokens, array $analysis): void
    {
        if (isset($analysis['finally'])) {
            if (!$analysis['finally']['empty']) {
                return;
            }

            $this->cleanupFinallyBlock($tokens, $analysis['finally']);

            if (0 === \count($analysis['catch']['blocks'])) {
                $this->cleanupTryBlock($tokens, $analysis['try']);

                return;
            }
        }

        if ($analysis['catch']['all_catch_only_throws']) {
            foreach (array_reverse($analysis['catch']['blocks']) as $catchAnalysis) {
                $this->cleanupCatchBlock($tokens, $catchAnalysis);
            }

            $this->cleanupTryBlock($tokens, $analysis['try']);
        }
    }

    /**
     * @return iterable<array{
     *     try: array{
     *         index: int,
     *         block_open: int,
     *         block_close: int,
     *     },
     *     catch: array{
     *         all_catch_only_throws: bool,
     *         blocks: list<array{
     *             only_catch_throws: bool,
     *             index?: int,
     *             open?: int,
     *             close?: int,
     *             block_open?: int,
     *             throw?: int,
     *             throw_var?: int,
     *             throw_semicolon?: int,
     *             block_close: int,
     *         }>,
     *     },
     *     finally?: array{
     *         empty: bool,
     *         index: int,
     *         block_open: int,
     *         block_close: int|null,
     *     },
     * }>
     */
    private function getTryCatchFinallyAnalysis(Tokens $tokens): iterable
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_TRY)) {
                continue;
            }

            $tryCurlyOpenIndex = $tokens->getNextMeaningfulToken($index);
            $tryCurlyCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $tryCurlyOpenIndex);

            $analysis = [
                'try' => [
                    'index' => $index,
                    'block_open' => $tryCurlyOpenIndex,
                    'block_close' => $tryCurlyCloseIndex,
                ],
                'catch' => [
                    'blocks' => [],
                ],
            ];

            // iterate all `catch` blocks and the `finally` block
            $closeIndex = $tryCurlyCloseIndex;

            while (true) {
                $elementIndex = $tokens->getNextMeaningfulToken($closeIndex);

                if (null === $elementIndex) {
                    break;
                }

                if ($tokens[$elementIndex]->isGivenKind(T_CATCH)) {
                    $catchAnalysis = $this->getCatchAnalysis($tokens, $elementIndex); // there must be `catch` block here
                    $analysis['catch']['blocks'][] = $catchAnalysis;

                    $closeIndex = $catchAnalysis['block_close'];
                } elseif ($tokens[$elementIndex]->isGivenKind(T_FINALLY)) {
                    $analysis['finally'] = $this->getFinallyAnalysis($tokens, $elementIndex);

                    break; // as `finally` is always the last block it is not possible there are more following
                } else {
                    break;
                }
            }

            if (0 === \count($analysis['catch']['blocks'])) {
                $allCatchOnlyThrows = false;
            } else {
                $allCatchOnlyThrows = true;

                foreach ($analysis['catch']['blocks'] as $catchBlock) {
                    if (!$catchBlock['only_catch_throws']) {
                        $allCatchOnlyThrows = false;

                        break;
                    }
                }
            }

            $analysis['catch']['all_catch_only_throws'] = $allCatchOnlyThrows;

            yield $analysis;
        }
    }

    /**
     * @return array{
     *     only_catch_throws: bool,
     *     index?: int,
     *     open?: int,
     *     close?: int,
     *     block_open?: int,
     *     throw?: int,
     *     throw_var?: int,
     *     throw_semicolon?: int,
     *     block_close: int,
     * }
     */
    private function getCatchAnalysis(Tokens $tokens, int $catchIndex): array
    {
        $catchBlock = ['only_catch_throws' => false];
        $braceOpenIndex = $tokens->getNextMeaningfulToken($catchIndex); // `(`
        $varCatchIndex = $tokens->getNextTokenOfKind($braceOpenIndex, [')', [T_VARIABLE]]); // caught `variable` candidate

        if (!$tokens[$varCatchIndex]->isGivenKind(T_VARIABLE)) {
            $curlyOpenIndex = $tokens->getNextTokenOfKind($varCatchIndex, ['{']);
            $catchBlock['block_close'] = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $curlyOpenIndex);

            return $catchBlock;
        }

        $braceCloseIndex = $tokens->getNextTokenOfKind($braceOpenIndex, [')']);
        $curlyOpenIndex = $tokens->getNextMeaningfulToken($braceCloseIndex); // `{`
        $throwIndex = $tokens->getNextMeaningfulToken($curlyOpenIndex); // `throw` candidate

        if (!$tokens[$throwIndex]->isGivenKind(T_THROW)) {
            $catchBlock['block_close'] = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $curlyOpenIndex);

            return $catchBlock;
        }

        $varThrowIndex = $tokens->getNextMeaningfulToken($throwIndex); // thrown `variable` candidate

        if (!$tokens[$varThrowIndex]->isGivenKind(T_VARIABLE) || $tokens[$varThrowIndex]->getContent() !== $tokens[$varCatchIndex]->getContent()) {
            $catchBlock['block_close'] = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $curlyOpenIndex);

            return $catchBlock;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($varThrowIndex); // `;` candidate

        if (!$tokens[$semicolonIndex]->equals(';')) {
            $catchBlock['block_close'] = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $curlyOpenIndex);

            return $catchBlock;
        }

        $curlyCloseIndex = $tokens->getNextMeaningfulToken($semicolonIndex); // close `}` candidate

        if (!$tokens[$curlyCloseIndex]->equals('}')) {
            $catchBlock['block_close'] = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $curlyOpenIndex);

            return $catchBlock;
        }

        return [
            'only_catch_throws' => true,
            'index' => $catchIndex,
            'open' => $braceOpenIndex,
            'close' => $braceCloseIndex,
            'block_open' => $curlyOpenIndex,
            'throw' => $throwIndex,
            'throw_var' => $varThrowIndex,
            'throw_semicolon' => $semicolonIndex,
            'block_close' => $curlyCloseIndex,
        ];
    }

    /**
     * @return array{
     *     empty: bool,
     *     index: int,
     *     block_open: int,
     *     block_close: int|null,
     * }
     */
    private function getFinallyAnalysis(Tokens $tokens, int $finallyIndex): array
    {
        $curlyOpenIndex = $tokens->getNextMeaningfulToken($finallyIndex); // `{`
        $curlyCloseIndex = $tokens->getNextMeaningfulToken($curlyOpenIndex); // `}` candidate
        $empty = $tokens[$curlyCloseIndex]->equals('}');

        return [
            'empty' => $empty,
            'index' => $finallyIndex,
            'block_open' => $curlyOpenIndex,
            'block_close' => $empty ? $curlyCloseIndex : null,
        ];
    }

    /**
     * @param array{
     *     index: int,
     *     block_open: int,
     *     block_close: int,
     * } $tryAnalysis
     */
    private function cleanupTryBlock(Tokens $tokens, array $tryAnalysis): void
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryAnalysis['block_close']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryAnalysis['block_open']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryAnalysis['index']);
    }

    /**
     * @param array{
     *     index: int,
     *     block_open: int,
     *     block_close: int,
     * } $finallyAnalysis
     */
    private function cleanupFinallyBlock(Tokens $tokens, array $finallyAnalysis): void
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($finallyAnalysis['block_close']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($finallyAnalysis['block_open']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($finallyAnalysis['index']);
    }

    /**
     * @param array{
     *     index: int,
     *     open: int,
     *     close: int,
     *     block_open: int,
     *     throw: int,
     *     throw_var: int,
     *     throw_semicolon: int,
     *     block_close: int,
     * } $catchAnalysis
     */
    private function cleanupCatchBlock(Tokens $tokens, array $catchAnalysis): void
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['block_close']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['throw_semicolon']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['throw_var']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['throw']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['block_open']);

        $toClean = $catchAnalysis['close'];

        do {
            $tokens->clearTokenAndMergeSurroundingWhitespace($toClean);
            $toClean = $tokens->getPrevMeaningfulToken($toClean);
        } while ($toClean >= $catchAnalysis['open']);

        $tokens->clearTokenAndMergeSurroundingWhitespace($catchAnalysis['index']);
    }
}
