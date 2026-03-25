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

namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Alexander M. Turek <me@derrabus.de>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ModernizeArrayKeyFunctionsFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replace `$array[array_key_first($array)]` with `array_first($array)` and `$array[array_key_last($array)]` with `array_last($array)`.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        $foo = [1, 2, 3];
                        $first = $foo[array_key_first($foo)];
                        $last = $foo[array_key_last($foo)];
                        $first = $foo->bar[array_key_first($foo->bar)] ?? null;
                        $last = FooClass::CONSTANT[array_key_last(FooClass::CONSTANT)] ?? null;

                        PHP,
                ),
            ],
            null,
            'This changes the behaviour for empty arrays: `$foo[array_key_first($foo)]` crashes with an invalid array offset error (unless it is caught using the `??` operator), `array_first($foo)` returns null instead. Also risky if the `array_first`, `array_last`, `array_key_first` or `array_key_last` functions are overridden.',
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_05_00 && $tokens->isTokenKindFound(\T_STRING);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        $modernizeCandidates = [[\T_STRING, 'array_key_first'], [\T_STRING, 'array_key_last']];

        $count = $tokens->count();
        for ($index = 0; $index < $count; ++$index) {
            // find candidate function call
            if (!$tokens[$index]->equalsAny($modernizeCandidates, false) || !$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $bracketsOpen = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$bracketsOpen]->equals('[')) {
                continue;
            }

            $endVariable = $tokens->getPrevMeaningfulToken($bracketsOpen);
            $beginVariable = $this->detectVariableEndingAtIndex($tokens, $endVariable);
            if (null === $beginVariable) {
                continue;
            }

            $parensOpen = $tokens->getNextMeaningfulToken($index);
            $parensClose = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $parensOpen);

            $bracketsClose = $tokens->getNextMeaningfulToken($parensClose);
            if (!$tokens[$bracketsClose]->equals(']')) {
                continue;
            }
            $equals = $tokens->getNextTokenOfKind($bracketsClose, ['=', [\T_AND_EQUAL], [\T_COALESCE_EQUAL], [\T_CONCAT_EQUAL], [\T_DIV_EQUAL], [\T_MINUS_EQUAL], [\T_MOD_EQUAL], [\T_MUL_EQUAL], [\T_OR_EQUAL], [\T_SL_EQUAL], [\T_SR_EQUAL], [\T_XOR_EQUAL]]);
            $endOfStatement = $tokens->getNextTokenOfKind($bracketsClose, [';', [\T_CLOSE_TAG], ',']);
            // avoid modifying the left-hand side of assignment operations
            if (null !== $equals && (null === $endOfStatement || $equals < $endOfStatement)) {
                continue;
            }

            $arguments = $argumentsAnalyzer->getArguments($tokens, $parensOpen, $parensClose);

            if (1 !== \count($arguments)) {
                continue;
            }
            $beginArgument = array_key_first($arguments);
            $endArgument = array_first($arguments);
            if (!$this->checkStatementsMatch($tokens, $beginVariable, $endVariable, $beginArgument, $endArgument)) {
                continue;
            }

            if ($tokens[$index]->equals([\T_STRING, 'array_key_first'])) {
                $functionName = 'array_first';
            } else {
                $functionName = 'array_last';
            }
            $resultItems = [new Token([\T_STRING, $functionName]), $tokens[$parensOpen]];
            for ($i = $beginVariable; $i <= $endVariable; ++$i) {
                $resultItems[] = $tokens[$i];
            }
            $resultItems[] = $tokens[$parensClose];
            $tokens->overrideRange($beginVariable, $bracketsClose, $resultItems);
            $index = $parensClose + 1;
        }
    }

    private function detectVariableEndingAtIndex(Tokens $tokens, ?int $index): ?int
    {
        if (null === $index) {
            return null;
        }
        if ($tokens[$index]->equals(']')) {
            $bracketOpen = $tokens->getPrevTokenOfKind($index, ['[']);

            return $this->detectVariableEndingAtIndex($tokens, $tokens->getPrevMeaningfulToken($bracketOpen));
        }
        if ($tokens[$index]->isGivenKind([\T_STRING, \T_VARIABLE])) {
            $previous = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$previous]->isGivenKind(Token::getObjectOperatorKinds())) {
                return $this->detectVariableEndingAtIndex($tokens, $tokens->getPrevMeaningfulToken($previous));
            }
            if ($tokens[$previous]->isGivenKind(\T_PAAMAYIM_NEKUDOTAYIM)) {
                return $this->detectVariableEndingAtIndex($tokens, $tokens->getPrevMeaningfulToken($previous));
            }

            return $index;
        }

        return null;
    }

    /**
     * Check that the two given token ranges contain the same set of meaningful tokens.
     */
    private function checkStatementsMatch(Tokens $tokens, ?int $beginA, int $endA, ?int $beginB, int $endB): bool
    {
        while ($beginA <= $endA && $beginB <= $endB) {
            if (!$tokens[$beginA]->equals($tokens[$beginB])) {
                return false;
            }
            // check that we reached the end of both statements; If only one of them has ended, they're a different length!
            if ($beginA === $endA) {
                return $beginB === $endB;
            }
            if ($beginB === $endB) {
                return false;
            }
            $beginA = $tokens->getNextMeaningfulToken($beginA);
            $beginB = $tokens->getNextMeaningfulToken($beginB);
            // this shouldn't happen as we should reach the end[AB] indices first
            if (null === $beginA || null === $beginB) {
                return false;
            }
        }

        // this shouldn't happen as we should return out from the loop
        return false;
    }
}
