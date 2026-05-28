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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class IsNullFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replaces `is_null($var)` expression with `null === $var`.',
            [
                new CodeSample("<?php\n\$a = is_null(\$b);\n"),
            ],
            null,
            'Risky when the function `is_null` is overridden.',
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before YodaStyleFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();
        $currIndex = 0;

        while (true) {
            // recalculate "end" because we might have added tokens in previous iteration
            $matches = $tokens->findSequence([[\T_STRING, 'is_null'], '('], $currIndex, $tokens->count() - 1, false);

            // stop looping if didn't find any new matches
            if (null === $matches) {
                break;
            }

            // 0 and 1 accordingly are "is_null", "(" tokens
            $matches = array_keys($matches);

            // move the cursor just after the sequence
            [$isNullIndex, $currIndex] = $matches;

            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $matches[0])) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($currIndex);

            if ($tokens[$next]->equals(')')) {
                continue;
            }

            $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);

            // handle function references with namespaces
            if ($tokens[$prevTokenIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                $tokens->removeTrailingWhitespace($prevTokenIndex);
                $tokens->clearAt($prevTokenIndex);

                $prevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
            }

            // check if inversion being used, text comparison is due to not existing constant
            $isInvertedNullCheck = false;

            if ($tokens[$prevTokenIndex]->equals('!')) {
                $isInvertedNullCheck = true;

                // get rid of inverting for proper transformations
                $tokens->removeTrailingWhitespace($prevTokenIndex);
                $tokens->clearAt($prevTokenIndex);
            }

            // before getting rind of `()` around a parameter, ensure it's not assignment/ternary invariant
            $referenceEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $matches[1]);
            $isContainingDangerousConstructs = false;

            for ($paramTokenIndex = $matches[1]; $paramTokenIndex <= $referenceEnd; ++$paramTokenIndex) {
                if (\in_array($tokens[$paramTokenIndex]->getContent(), ['?', '?:', '=', '??'], true)) {
                    $isContainingDangerousConstructs = true;

                    break;
                }
            }

            // edge cases: is_null() followed/preceded by ==, ===, !=, !==, <>, (int-or-other-casting)
            $parentLeftToken = $tokens[$tokens->getPrevMeaningfulToken($isNullIndex)];
            $parentRightToken = $tokens[$tokens->getNextMeaningfulToken($referenceEnd)];
            $parentOperations = [\T_IS_EQUAL, \T_IS_NOT_EQUAL, \T_IS_IDENTICAL, \T_IS_NOT_IDENTICAL];
            $wrapIntoParentheses = $parentLeftToken->isCast() || $parentLeftToken->isGivenKind($parentOperations) || $parentRightToken->isGivenKind($parentOperations);

            // possible trailing comma removed
            $prevIndex = $tokens->getPrevMeaningfulToken($referenceEnd);

            if ($tokens[$prevIndex]->equals(',')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($prevIndex);
            }

            if (!$isContainingDangerousConstructs) {
                // closing parenthesis removed with leading spaces
                $tokens->removeLeadingWhitespace($referenceEnd);
                $tokens->clearAt($referenceEnd);

                // opening parenthesis removed with trailing spaces
                $tokens->removeLeadingWhitespace($matches[1]);
                $tokens->removeTrailingWhitespace($matches[1]);
                $tokens->clearAt($matches[1]);
            }

            // sequence which we'll use as a replacement
            $replacement = [
                new Token([\T_STRING, 'null']),
                new Token([\T_WHITESPACE, ' ']),
                new Token($isInvertedNullCheck ? [\T_IS_NOT_IDENTICAL, '!=='] : [\T_IS_IDENTICAL, '===']),
                new Token([\T_WHITESPACE, ' ']),
            ];

            if ($wrapIntoParentheses) {
                array_unshift($replacement, new Token('('));
                $tokens->insertAt($referenceEnd + 1, new Token(')'));
            }

            $tokens->overrideRange($isNullIndex, $isNullIndex, $replacement);

            // nested is_null calls support
            $currIndex = $isNullIndex;
        }
    }
}
