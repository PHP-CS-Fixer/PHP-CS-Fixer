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
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind(T_FINALLY)) {
                $this->fixFinallyBlock($tokens, $index);
            } elseif ($tokens[$index]->isGivenKind(T_CATCH)) {
                $this->fixTryCatchBlock($tokens, $index);
            }
        }
    }

    private function fixFinallyBlock(Tokens $tokens, int $index): void
    {
        $braceOpenIndex = $tokens->getNextMeaningfulToken($index); // `{`
        $braceCloseIndex = $tokens->getNextMeaningfulToken($braceOpenIndex); // `}` candidate

        if (!$tokens[$braceCloseIndex]->equals('}')) {
            return; // `finally` block is not empty
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($braceCloseIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($braceOpenIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);

        // check if there is a `catch` block that was before the `finally`, if not remove the `try {}` part as well

        $tryCloseBraceCandidateIndex = $tokens->getPrevMeaningfulToken($braceOpenIndex);
        $tryOpenBraceCandidateIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $tryCloseBraceCandidateIndex);
        $tryCandidateIndex = $tokens->getPrevMeaningfulToken($tryOpenBraceCandidateIndex);

        if (!$tokens[$tryCandidateIndex]->isGivenKind(T_TRY)) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($tryCloseBraceCandidateIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryOpenBraceCandidateIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryCandidateIndex);
    }

    private function fixTryCatchBlock(Tokens $tokens, int $index): void
    {
        $braceOpenIndex = $tokens->getNextMeaningfulToken($index); // `(`
        $varCatchIndex = $tokens->getNextTokenOfKind($braceOpenIndex, [')', [T_VARIABLE]]); // caught `variable` candidate

        if (!$tokens[$varCatchIndex]->isGivenKind(T_VARIABLE)) {
            return;
        }

        $braceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $braceOpenIndex);
        $blockOpenIndex = $tokens->getNextMeaningfulToken($braceCloseIndex); // `{`
        $throwIndex = $tokens->getNextMeaningfulToken($blockOpenIndex); // `throw` candidate

        if (!$tokens[$throwIndex]->isGivenKind(T_THROW)) {
            return;
        }

        $varThrowIndex = $tokens->getNextMeaningfulToken($throwIndex); // thrown `variable` candidate

        if (!$tokens[$varThrowIndex]->isGivenKind(T_VARIABLE) || $tokens[$varThrowIndex]->getContent() !== $tokens[$varCatchIndex]->getContent()) {
            return;
        }

        $semicolonIndex = $tokens->getNextMeaningfulToken($varThrowIndex); // `;` candidate

        if (!$tokens[$semicolonIndex]->equals(';')) {
            return;
        }

        $blockCloseIndex = $tokens->getNextMeaningfulToken($semicolonIndex); // close `}` candidate

        if (!$tokens[$blockCloseIndex]->equals('}')) {
            return;
        }

        $finallyIndex = $tokens->getNextMeaningfulToken($blockCloseIndex); // `finally` candidate

        if (null !== $finallyIndex && $tokens[$finallyIndex]->isGivenKind(T_FINALLY)) {
            return;
        }

        // gather other indexes to clean
        $tryCloseIndex = $tokens->getPrevMeaningfulToken($index);
        $tryOpenIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $tryCloseIndex);
        $tryIndex = $tokens->getPrevMeaningfulToken($tryOpenIndex);

        // clean up the statement
        $tokens->clearTokenAndMergeSurroundingWhitespace($blockCloseIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($semicolonIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($varThrowIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($throwIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($blockOpenIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($braceCloseIndex);

        $clearIndex = $braceCloseIndex;

        do {
            $clearIndex = $tokens->getPrevMeaningfulToken($clearIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($clearIndex);
        } while ($clearIndex > $braceOpenIndex);

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryOpenIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($tryCloseIndex);
    }
}
