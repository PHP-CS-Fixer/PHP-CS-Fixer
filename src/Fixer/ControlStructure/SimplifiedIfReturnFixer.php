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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @phpstan-import-type _PhpTokenPrototypePartial from Token
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class SimplifiedIfReturnFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Simplify `if` control structures that return the boolean result of their condition.',
            [new CodeSample("<?php\nif (\$foo) { return true; } return false;\n")],
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer.
     * Must run after NoSuperfluousElseifFixer, NoUnneededBracesFixer, NoUnneededCurlyBracesFixer, NoUselessElseFixer, SemicolonAfterInstructionFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([\T_IF, \T_RETURN, \T_STRING]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $slices = [];
        $nextIfIndex = null;

        for ($ifIndex = $tokens->count() - 1; 0 <= $ifIndex; --$ifIndex) {
            $id = $tokens[$ifIndex]->getId();

            // much faster to check the token type directly than via Token::isGivenKind().
            if (\T_IF !== $id && \T_ELSEIF !== $id) {
                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($ifIndex)]->equals(')')) {
                continue; // in a loop without braces
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($ifIndex, ['(']);
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $startParenthesisIndex);
            $firstCandidateIndex = $tokens->getNextMeaningfulToken($endParenthesisIndex);

            $match = $this->matchReturnSequence($tokens, $firstCandidateIndex);

            if (null === $match) {
                continue;
            }

            if ($match['indices'][0] !== $firstCandidateIndex) {
                continue;
            }

            $indicesToClear = $match['indices'];
            array_pop($indicesToClear); // Preserve last semicolon
            rsort($indicesToClear);

            foreach ($indicesToClear as $index) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }

            $newTokens = [
                new Token([\T_RETURN, 'return']),
                new Token([\T_WHITESPACE, ' ']),
            ];

            $newTokens[] = $match['isNegative']
                ? new Token('!')
                : new Token([\T_BOOL_CAST, '(bool)']);

            $slices[$ifIndex] = $newTokens;
            $tokens->clearAt($ifIndex);
        }

        if ([] !== $slices) {
            $tokens->insertSlices($slices);
        }
    }

    /**
     * @return null|array{isNegative: bool, indices: list{0: int, 1: int, 2: int, 3: int, 4: int, 5: int, 6?: int, 7?: int}}
     */
    private function matchReturnSequence(Tokens $tokens, int $start): ?array
    {
        $count = $tokens->count();

        for ($return = $start; $return < $count; ++$return) {
            $id = $tokens[$return]->getId();

            // Avoid scanning past another conditional because a valid
            // continuation of the original pattern is no longer possible.
            if (\T_IF === $id || \T_ELSEIF === $id) {
                break;
            }

            if (\T_RETURN !== $id) {
                continue;
            }

            $bool1 = $tokens->getNextMeaningfulToken($return);
            if (null === $bool1 || \T_STRING !== $tokens[$bool1]->getId()) {
                continue;
            }

            $value1 = $tokens[$bool1]->getContent();
            if ('true' !== $value1 && 'false' !== $value1) {
                continue;
            }

            $semi1 = $tokens->getNextMeaningfulToken($bool1);
            if (null === $semi1 || ';' !== $tokens[$semi1]->getContent()) {
                continue;
            }

            $indices = [];

            $prev = $tokens->getPrevMeaningfulToken($return);
            if (null !== $prev && $tokens[$prev]->equals('{')) {
                $indices[] = $prev;
            }

            $indices[] = $return;
            $indices[] = $bool1;
            $indices[] = $semi1;

            $next = $tokens->getNextMeaningfulToken($semi1);
            if (null === $next) {
                continue;
            }

            if ($tokens[$next]->equals('}')) {
                $indices[] = $next;

                $next = $tokens->getNextMeaningfulToken($next);
                if (null === $next) {
                    continue;
                }
            }

            if (\T_RETURN !== $tokens[$next]->getId()) {
                continue;
            }

            $indices[] = $next;

            $bool2 = $tokens->getNextMeaningfulToken($next);
            if (null === $bool2 || \T_STRING !== $tokens[$bool2]->getId()) {
                continue;
            }

            $value2 = $tokens[$bool2]->getContent();

            if (('true' !== $value2 && 'false' !== $value2) || $value1 === $value2) {
                continue;
            }

            $indices[] = $bool2;

            $semi2 = $tokens->getNextMeaningfulToken($bool2);
            if (null === $semi2 || ';' !== $tokens[$semi2]->getContent()) {
                continue;
            }

            $indices[] = $semi2;

            return [
                'isNegative' => 'false' === $value1,
                'indices' => $indices,
            ];
        }

        return null;
    }
}
