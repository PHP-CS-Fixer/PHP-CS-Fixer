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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Nat Zimmermann <nathanielzimmermann@gmail.com>
 */
final class NoEmptyBlockFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There must be no empty blocks. Blocks with comment inside are NOT considered as empty.',
            [
                new CodeSample("<?php if (\$foo) {}\n"),
                new CodeSample("<?php switch (\$foo) {}\n"),
                new CodeSample("<?php while (\$foo) {}\n"),
            ],
            null,
            'Risky if the block has side effects.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoTrailingWhitespaceFixer.
     * Must run after NoEmptyCommentFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isRisky(): bool
    {
        return true;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            T_DO,
            T_ELSE,
            T_FINALLY,
            T_FOR,
            T_IF,
            T_SWITCH,
            T_TRY,
            T_WHILE,
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_DO)) {
                $this->fixDoWhile($index, $tokens);
            } elseif ($token->isGivenKind(T_ELSE)) {
                $this->fixElse($index, $tokens);
            } elseif ($token->isGivenKind(T_FINALLY)) {
                $this->fixFinally($index, $tokens);
            } elseif ($token->isGivenKind(T_FOR)) {
                $this->fixFor($index, $tokens);
            } elseif ($token->isGivenKind(T_IF)) {
                $this->fixIf($index, $tokens);
            } elseif ($token->isGivenKind(T_SWITCH)) {
                $this->fixSwitch($index, $tokens);
            } elseif ($token->isGivenKind(T_TRY)) {
                $this->fixTry($index, $tokens);
            } elseif ($token->isGivenKind(T_WHILE)) {
                $this->fixWhile($index, $tokens);
            }
        }
    }

    private function fixDoWhile(int $doIndex, Tokens $tokens): void
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($doIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $whileIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
        $openBraceIndex = $tokens->getNextMeaningfulToken($whileIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);
        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $doIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $doIndex, $closeBraceIndex);
    }

    private function fixElse(int $elseIndex, Tokens $tokens): void
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($elseIndex);

        if ($tokens[$openBodyIndex]->equals(':')) {
            $endifIndex = $tokens->getNextNonWhitespace($openBodyIndex);

            if ($tokens[$endifIndex]->isGivenKind(T_ENDIF)) {
                // keep the endif as the if statement will break without it
                $this->clearRangeKeepComments($tokens, $elseIndex, $openBodyIndex);
            }

            return;
        }

        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $this->clearRangeKeepComments($tokens, $elseIndex, $closeBodyIndex);
    }

    private function fixFinally(int $finallyIndex, Tokens $tokens): void
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($finallyIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $this->clearRangeKeepComments($tokens, $finallyIndex, $closeBodyIndex);
    }

    private function fixFor(int $forIndex, Tokens $tokens): void
    {
        $this->fixBraceBlock($tokens, $forIndex, T_ENDFOR);
    }

    private function fixIf(int $ifIndex, Tokens $tokens): void
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($ifIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);
        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        while (null !== $closeBodyIndex) {
            if ($tokens[$closeBodyIndex]->equals('}')) {
                $nextIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

                if (null === $nextIndex) {
                    $this->clearRangeKeepComments($tokens, $ifIndex, $closeBodyIndex);

                    return;
                }

                $closeBodyIndex = $nextIndex;
            }

            if ($tokens[$closeBodyIndex]->isGivenKind(T_ELSE)) {
                // if `else` still exists, it means that it has a body, as
                // `fixElse` is run before this
                return;
            }

            if ($tokens[$closeBodyIndex]->isGivenKind(T_ENDIF)) {
                $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
                $endIndex = $tokens[$semicolonIndex]->equals(';') ? $semicolonIndex : $closeBodyIndex;

                $this->clearRangeKeepComments($tokens, $ifIndex, $endIndex);

                return;
            }

            if (!$tokens[$closeBodyIndex]->isGivenKind(T_ELSEIF)) {
                return;
            }

            $openElseifBraceIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);
            $closeElseifBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openElseifBraceIndex);
            $openElseifBodyIndex = $tokens->getNextMeaningfulToken($closeElseifBraceIndex);
            $closeBodyIndex = $tokens->getNextNonWhitespace($openElseifBodyIndex);
        }
    }

    private function fixSwitch(int $switchIndex, Tokens $tokens): void
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($switchIndex);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);
        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $this->clearRangeKeepComments($tokens, $switchIndex, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind(T_ENDSWITCH)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $switchIndex, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $switchIndex, $closeBodyIndex);
    }

    private function fixTry(int $tryIndex, Tokens $tokens): void
    {
        $openBodyIndex = $tokens->getNextMeaningfulToken($tryIndex);
        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if (!$tokens[$closeBodyIndex]->equals('}')) {
            return;
        }

        $clearRangeIndexEnd = $closeBodyIndex;
        $catchOrFinallyIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        while (null !== $catchOrFinallyIndex && $tokens[$catchOrFinallyIndex]->isGivenKind(T_CATCH)) {
            $openCatchBraceIndex = $tokens->getNextMeaningfulToken($catchOrFinallyIndex);
            $closeCatchBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openCatchBraceIndex);
            $openCatchBodyIndex = $tokens->getNextMeaningfulToken($closeCatchBraceIndex);
            $closeCatchBodyIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openCatchBodyIndex);
            $clearRangeIndexEnd = $closeCatchBodyIndex;
            $catchOrFinallyIndex = $tokens->getNextMeaningfulToken($closeCatchBodyIndex);
        }

        if (null !== $catchOrFinallyIndex && $tokens[$catchOrFinallyIndex]->isGivenKind(T_FINALLY)) {
            $openFinallyBodyIndex = $tokens->getNextMeaningfulToken($catchOrFinallyIndex);
            $closeFinallyBodyIndex = $tokens->getNextNonWhitespace($openFinallyBodyIndex);

            if (!$tokens[$closeFinallyBodyIndex]->equals('}')) {
                return;
            }

            $clearRangeIndexEnd = $closeFinallyBodyIndex;
        }

        $this->clearRangeKeepComments($tokens, $tryIndex, $clearRangeIndexEnd);
    }

    private function fixWhile(int $whileIndex, Tokens $tokens): void
    {
        // make sure it's not part of a do-while statement
        $closeDoBodyIndex = $tokens->getPrevMeaningfulToken($whileIndex);

        if ($tokens[$closeDoBodyIndex]->equals('}')) {
            $openDoBodyIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $closeDoBodyIndex);
            $doIndex = $tokens->getPrevMeaningfulToken($openDoBodyIndex);

            if ($tokens[$doIndex]->isGivenKind(T_DO)) {
                return;
            }
        }

        $this->fixBraceBlock($tokens, $whileIndex, T_ENDWHILE);
    }

    private function clearRangeKeepComments(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        for ($index = $endIndex; $startIndex <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }
        }
    }

    private function fixBraceBlock(Tokens $tokens, int $index, int $alternativeSyntaxEndType): void
    {
        $openBraceIndex = $tokens->getNextMeaningfulToken($index);
        $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);
        $openBodyIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);
        $openBody = $tokens[$openBodyIndex];

        if ($openBody->isGivenKind(T_CLOSE_TAG)) {
            $this->clearRangeKeepComments($tokens, $index, $closeBraceIndex);

            return;
        }

        if ($openBody->equals(';')) {
            $this->clearRangeKeepComments($tokens, $index, $openBodyIndex);

            return;
        }

        $closeBodyIndex = $tokens->getNextNonWhitespace($openBodyIndex);

        if ($tokens[$closeBodyIndex]->equals('}')) {
            $this->clearRangeKeepComments($tokens, $index, $closeBodyIndex);

            return;
        }

        if (!$tokens[$closeBodyIndex]->isGivenKind($alternativeSyntaxEndType)) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($closeBodyIndex);

        if ($tokens[$semicolonIndex]->equals(';')) {
            $this->clearRangeKeepComments($tokens, $index, $semicolonIndex);

            return;
        }

        $this->clearRangeKeepComments($tokens, $index, $closeBodyIndex);
    }
}
