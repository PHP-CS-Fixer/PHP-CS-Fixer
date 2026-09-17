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
            null,
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

        $classyBraces = $this->getClassyBraces($tokens);
        $parameterLists = $this->getParameterLists($tokens);

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION) || !$analyzer->isLambda($index)) {
                continue;
            }

            // Constant expressions allow closures as of PHP 8.5, but never arrow
            // functions, so converting one there yields code that cannot compile.
            if ($this->isInConstantExpression($tokens, $index, $classyBraces, $parameterLists)) {
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

            // Transform the function to an arrow function
            $this->transform($tokens, $index, $useStart, $useEnd, $braceOpen, $return, $semicolon, $braceClose);
        }
    }

    /**
     * Whether the closure at the given index belongs to a constant expression.
     *
     * The innermost enclosing scope decides: the body of a closure declared in a
     * constant expression is ordinary runtime code, while a default value nested
     * inside a runtime closure's signature is still a constant expression.
     *
     * @param array<int, true> $classyBraces
     * @param array<int, true> $parameterLists
     */
    private function isInConstantExpression(Tokens $tokens, int $index, array $classyBraces, array $parameterLists): bool
    {
        $position = $index;

        while (true) {
            $open = $this->getEnclosingBlockStart($tokens, $position);

            if (null === $open) {
                return $this->isWithinConstantDeclaration($tokens, $position);
            }

            $blockType = Tokens::detectBlockType($tokens[$open]);

            // Attribute arguments are constant expressions.
            if (Tokens::BLOCK_TYPE_ATTRIBUTE === $blockType['type']) {
                return true;
            }

            // A property hook holds runtime code, unlike the default value of
            // the property it belongs to.
            if (Tokens::BLOCK_TYPE_PROPERTY_HOOK === $blockType['type']) {
                return false;
            }

            if (Tokens::BLOCK_TYPE_BRACE === $blockType['type']) {
                // Directly in a classy body, so within a constant or a property
                // default value. Any other brace opens runtime code, be it a
                // function body or a control structure.
                return isset($classyBraces[$open]);
            }

            if (Tokens::BLOCK_TYPE_PARENTHESIS === $blockType['type']) {
                // Default values of parameters are constant expressions.
                if (isset($parameterLists[$open])) {
                    return true;
                }
            }

            // Arrays, argument lists and groupings take the surrounding context.
            $position = $open;
        }
    }

    /**
     * Get the start of the innermost block the given index sits in, if any.
     */
    private function getEnclosingBlockStart(Tokens $tokens, int $position): ?int
    {
        $depth = 0;

        for ($index = $position - 1; $index >= 0; --$index) {
            $blockType = Tokens::detectBlockType($tokens[$index]);

            if (null === $blockType) {
                continue;
            }

            if (!$blockType['isStart']) {
                ++$depth;

                continue;
            }

            if (0 === $depth) {
                return $index;
            }

            --$depth;
        }

        return null;
    }

    /**
     * Whether the statement the given index belongs to declares a constant.
     */
    private function isWithinConstantDeclaration(Tokens $tokens, int $position): bool
    {
        for ($index = $position - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            $blockType = Tokens::detectBlockType($token);

            // Jump over a complete block, such as an index or a grouping, that
            // the declaration may hold before the closure.
            if (null !== $blockType && !$blockType['isStart']) {
                $index = $tokens->findBlockStart($blockType['type'], $index);

                continue;
            }

            if ($token->isGivenKind(\T_CONST)) {
                return true;
            }

            if ($token->equals(';') || $token->isGivenKind(\T_CLOSE_TAG)) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get the braces that open the body of a class, interface, trait or enum.
     *
     * @return array<int, true>
     */
    private function getClassyBraces(Tokens $tokens): array
    {
        $braces = [];

        foreach ($tokens as $index => $token) {
            if (!$token->isClassy()) {
                continue;
            }

            $position = $tokens->getNextMeaningfulToken($index);

            // Skip the argument list of an anonymous class, as it may itself
            // contain braces, for instance those of a closure passed to it.
            if (null !== $position && $tokens[$position]->equals('(')) {
                $position = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $position);
            }

            $brace = $tokens->getNextTokenOfKind($position, ['{']);

            if (null !== $brace) {
                $braces[$brace] = true;
            }
        }

        return $braces;
    }

    /**
     * Get the parentheses that open the parameter list of a declaration.
     *
     * @return array<int, true>
     */
    private function getParameterLists(Tokens $tokens): array
    {
        $parentheses = [];

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([\T_FUNCTION, \T_FN])) {
                continue;
            }

            $start = $tokens->getNextTokenOfKind($index, ['(']);

            if (null !== $start) {
                $parentheses[$start] = true;
            }
        }

        return $parentheses;
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
