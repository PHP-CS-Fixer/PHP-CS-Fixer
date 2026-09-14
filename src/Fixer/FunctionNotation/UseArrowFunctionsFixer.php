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
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class UseArrowFunctionsFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Anonymous functions with return as the only statement must use arrow functions.',
            [
                new CodeSample(
                    <<<'SAMPLE'
                        <?php
                        foo(function ($a) use ($b) {
                            return $a + $b;
                        });

                        SAMPLE,
                ),
            ],
            'Closures in constant expressions (attributes, constants, and property or parameter defaults) are not converted.',
            'Risky when using `isset()` on outside variables that are not imported with `use ()`.',
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([\T_FUNCTION, \T_RETURN]);
    }

    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FunctionDeclarationFixer.
     */
    public function getPriority(): int
    {
        return 32;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $analyzer = new TokensAnalyzer($tokens);
        $constantExpressionClosures = null;

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION) || !$analyzer->isLambda($index)) {
                continue;
            }

            // Find parameters

            $parametersStart = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$parametersStart]->isGivenKind(CT::T_RETURN_REF)) {
                $parametersStart = $tokens->getNextMeaningfulToken($parametersStart);
            }

            $parametersEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $parametersStart);

            // Find `use ()` start and end
            // Abort if it contains reference variables

            $next = $tokens->getNextMeaningfulToken($parametersEnd);

            $useStart = null;
            $useEnd = null;

            if ($tokens[$next]->isGivenKind(CT::T_USE_LAMBDA)) {
                $useStart = $next;

                if ($tokens[$useStart - 1]->isGivenKind(\T_WHITESPACE)) {
                    --$useStart;
                }

                $next = $tokens->getNextMeaningfulToken($next);

                while (!$tokens[$next]->equals(')')) {
                    if ($tokens[$next]->equals('&')) {
                        // variables used by reference are not supported by arrow functions
                        continue 2;
                    }

                    $next = $tokens->getNextMeaningfulToken($next);
                }

                $useEnd = $next;
                $next = $tokens->getNextMeaningfulToken($next);
            }

            // Find opening brace and following `return`
            // Abort if there is more than whitespace between them (like comments)

            $braceOpen = $tokens[$next]->equals('{') ? $next : $tokens->getNextTokenOfKind($next, ['{']);
            $return = $braceOpen + 1;

            if ($tokens[$return]->isGivenKind(\T_WHITESPACE)) {
                ++$return;
            }

            if (!$tokens[$return]->isGivenKind(\T_RETURN)) {
                continue;
            }

            // Find semicolon of `return` statement

            $semicolon = $tokens->getNextTokenOfKind($return, ['{', ';']);

            if (!$tokens[$semicolon]->equals(';')) {
                continue;
            }

            // Find closing brace
            // Abort if there is more than whitespace between semicolon and closing brace

            $braceClose = $semicolon + 1;

            if ($tokens[$braceClose]->isGivenKind(\T_WHITESPACE)) {
                ++$braceClose;
            }

            if (!$tokens[$braceClose]->equals('}')) {
                continue;
            }

            // Abort if closure has `use()` clause and return statement includes external files.
            // Converting such closures to arrow functions changes behaviour as the used variables
            // are no longer exposed to the included file.
            if (null !== $useStart && $this->containsIncludeOrRequire($tokens, $return, $semicolon)) {
                continue;
            }

            $constantExpressionClosures ??= $this->getConstantExpressionClosures($tokens);

            if (isset($constantExpressionClosures[$index])) {
                continue;
            }

            // Transform the function to an arrow function
            $this->transform($tokens, $index, $useStart, $useEnd, $braceOpen, $return, $semicolon, $braceClose);
        }
    }

    /**
     * Constant-expression restrictions do not extend into closure bodies or
     * property hooks: these contain ordinary executable code. Collect nested
     * scopes before fixing, then classify each closure in its innermost scope.
     * The backward fixing pass leaves earlier closure indices unchanged.
     *
     * @return array<int, true>
     */
    private function getConstantExpressionClosures(Tokens $tokens): array
    {
        /** @var list<array{start: int, end: int, constant: bool}> $scopes */
        $scopes = [];

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(FCT::T_ATTRIBUTE)) {
                $scopes[] = ['start' => $index + 1, 'end' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $index) - 1, 'constant' => true];
            } elseif ($token->isClassy()) {
                /** @var int $bodyOpen */
                $bodyOpen = $tokens->getNextTokenOfKind($index, ['{', '(', [CT::T_CLASS_INSTANTIATION_PARENTHESIS_OPEN]]);

                if (!$tokens[$bodyOpen]->equals('{')) {
                    $blockType = $tokens[$bodyOpen]->equals('(') ? Tokens::BLOCK_TYPE_PARENTHESIS : Tokens::BLOCK_TYPE_CLASS_INSTANTIATION_PARENTHESIS;
                    $argumentsEnd = $tokens->findBlockEnd($blockType, $bodyOpen);

                    /** @var int $bodyOpen */
                    $bodyOpen = $tokens->getNextTokenOfKind($argumentsEnd, ['{']);
                }

                $scopes[] = ['start' => $bodyOpen + 1, 'end' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_BRACE, $bodyOpen) - 1, 'constant' => true];
            } elseif ($token->isGivenKind([\T_FUNCTION, \T_FN])) {
                /** @var int $parametersOpen */
                $parametersOpen = $tokens->getNextTokenOfKind($index, ['(']);
                $parametersEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $parametersOpen);
                $scopes[] = ['start' => $parametersOpen + 1, 'end' => $parametersEnd - 1, 'constant' => true];

                if ($token->isGivenKind(\T_FUNCTION)) {
                    /** @var int $bodyOpen */
                    $bodyOpen = $tokens->getNextTokenOfKind($parametersEnd, ['{', ';']);

                    if ($tokens[$bodyOpen]->equals('{')) {
                        $scopes[] = ['start' => $bodyOpen + 1, 'end' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_BRACE, $bodyOpen) - 1, 'constant' => false];
                    }
                }
            } elseif ($token->isGivenKind(\T_CONST)) {
                $scopes[] = ['start' => $index + 1, 'end' => $this->findConstantDeclarationEnd($tokens, $index) - 1, 'constant' => true];
            } elseif ($token->isGivenKind(CT::T_PROPERTY_HOOK_BRACE_OPEN)) {
                $scopes[] = ['start' => $index + 1, 'end' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PROPERTY_HOOK, $index) - 1, 'constant' => false];
            }
        }

        $scopes = array_filter($scopes, static fn (array $scope): bool => $scope['start'] <= $scope['end']);
        usort($scopes, static fn (array $left, array $right): int => $left['start'] <=> $right['start']);

        $stack = [['end' => $tokens->count(), 'constant' => false]];
        $scopeIndex = 0;
        $closures = [];

        foreach ($tokens as $index => $token) {
            while (\count($stack) > 1 && $stack[\count($stack) - 1]['end'] < $index) {
                array_pop($stack);
            }

            while (isset($scopes[$scopeIndex]) && $scopes[$scopeIndex]['start'] === $index) {
                $stack[] = $scopes[$scopeIndex];
                ++$scopeIndex;
            }

            if ($token->isGivenKind(\T_FUNCTION) && $stack[\count($stack) - 1]['constant']) {
                $closures[$index] = true;
            }
        }

        return $closures;
    }

    private function findConstantDeclarationEnd(Tokens $tokens, int $index): int
    {
        while (!$tokens[$index]->equals(';') && !$tokens[$index]->isGivenKind(\T_CLOSE_TAG)) {
            $block = Tokens::detectBlockType($tokens[$index]);

            if (null !== $block && $block['isStart']) {
                $index = $tokens->findBlockEnd($block['type'], $index);
            }

            ++$index;
        }

        return $index;
    }

    private function transform(Tokens $tokens, int $index, ?int $useStart, ?int $useEnd, int $braceOpen, int $return, int $semicolon, int $braceClose): void
    {
        $tokensToInsert = [new Token([\T_DOUBLE_ARROW, '=>'])];

        if ($tokens->getNextMeaningfulToken($return) === $semicolon) {
            $tokensToInsert[] = new Token([\T_WHITESPACE, ' ']);
            $tokensToInsert[] = new Token([\T_STRING, 'null']);
        }

        $tokens->clearRange($semicolon, $braceClose - 1);
        $tokens->clearTokenAndMergeSurroundingWhitespace($braceClose);

        $tokens->clearRange($braceOpen + 1, $return);
        $tokens->overrideRange($braceOpen, $braceOpen, $tokensToInsert);

        if (null !== $useStart) {
            $tokens->clearRange($useStart, $useEnd);
        }

        $tokens[$index] = new Token([\T_FN, 'fn']);
    }

    /**
     * Check if the return statement contains include/include_once/require/require_once.
     */
    private function containsIncludeOrRequire(Tokens $tokens, int $start, int $end): bool
    {
        for ($i = $start; $i < $end; ++$i) {
            if ($tokens[$i]->isGivenKind([\T_INCLUDE, \T_INCLUDE_ONCE, \T_REQUIRE, \T_REQUIRE_ONCE])) {
                return true;
            }
        }

        return false;
    }
}
