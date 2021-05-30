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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Alexander M. Turek <me@derrabus.de>
 */
final class ModernizeStrposFixer extends AbstractFixer
{
    private const REPLACEMENTS = [
        [
            'operator' => [T_IS_IDENTICAL, '==='],
            'operand' => [T_LNUMBER, '0'],
            'replacement' => [T_STRING, 'str_starts_with'],
            'negate' => false,
        ],
        [
            'operator' => [T_IS_NOT_IDENTICAL, '!=='],
            'operand' => [T_LNUMBER, '0'],
            'replacement' => [T_STRING, 'str_starts_with'],
            'negate' => true,
        ],
        [
            'operator' => [T_IS_NOT_IDENTICAL, '!=='],
            'operand' => [T_STRING, 'false'],
            'replacement' => [T_STRING, 'str_contains'],
            'negate' => false,
        ],
        [
            'operator' => [T_IS_IDENTICAL, '==='],
            'operand' => [T_STRING, 'false'],
            'replacement' => [T_STRING, 'str_contains'],
            'negate' => true,
        ],
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replace `strpos()` expressions with `str_starts_with()` or `str_contains()` if possible.',
            [
                new CodeSample("<?php if (strpos(\$haystack, \$needle) === 0) {}\n"),
                new CodeSample("<?php if (strpos(\$haystack, \$needle) !== 0) {}\n"),
                new CodeSample("<?php if (strpos(\$haystack, \$needle) !== false) {}\n"),
                new CodeSample("<?php if (strpos(\$haystack, \$needle) === false) {}\n"),
            ],
            null,
            'Risky if the `strpos` function is overridden.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $seq = [[T_STRING, 'strpos'], '('];

        $currIndex = 0;
        while (null !== $currIndex) {
            $match = $tokens->findSequence($seq, $currIndex, null, false);
            if (null === $match) {
                break;
            }

            [$functionPos, $argumentsStart] = array_keys($match);
            $currIndex = $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);

            $operatorPos = $tokens->getNextMeaningfulToken($argumentsEnd);
            $operandPos = $tokens->getNextMeaningfulToken($operatorPos);

            foreach (self::REPLACEMENTS as [
                'operator' => $operator,
                'operand' => $operand,
                'replacement' => $replacement,
                'negate' => $negate,
            ]) {
                if (!$tokens[$operatorPos]->equals($operator) || !$tokens[$operandPos]->equals($operand)) {
                    continue;
                }

                for ($i = $argumentsEnd + 1; $i <= $operandPos; ++$i) {
                    $tokens[$i] = new Token('');
                }
                $tokens[$functionPos] = new Token($replacement);

                if ($negate) {
                    for ($i = $argumentsEnd; $i >= $functionPos; --$i) {
                        $token = $tokens[$i];
                        \assert(null !== $token);
                        $tokens[$i + 1] = $token;
                    }
                    $tokens[$functionPos] = new Token('!');
                }

                continue 2;
            }

            $operatorPos = $tokens->getPrevMeaningfulToken($functionPos);
            $operandPos = $tokens->getPrevMeaningfulToken($operatorPos);

            foreach (self::REPLACEMENTS as [
                'operator' => $operator,
                'operand' => $operand,
                'replacement' => $replacement,
                'negate' => $negate,
            ]) {
                if (!$tokens[$operatorPos]->equals($operator) || !$tokens[$operandPos]->equals($operand)) {
                    continue;
                }

                for ($i = $functionPos - 1; $i >= $operandPos; --$i) {
                    $tokens[$i] = new Token('');
                }
                $tokens[$functionPos] = new Token($replacement);

                if ($negate) {
                    $tokens[$functionPos - 1] = new Token('!');
                }

                continue 2;
            }
        }

        $tokens->clearEmptyTokens();
    }
}
