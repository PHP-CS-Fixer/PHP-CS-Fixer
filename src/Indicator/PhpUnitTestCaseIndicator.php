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

namespace PhpCsFixer\Indicator;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class PhpUnitTestCaseIndicator
{
    public function isPhpUnitClass(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isGivenKind(T_CLASS)) {
            throw new \LogicException(sprintf('No "T_CLASS" at given index %d, got "%s".', $index, $tokens[$index]->getName()));
        }

        $index = $tokens->getNextMeaningfulToken($index);

        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return false;
        }

        $extendsIndex = $tokens->getNextTokenOfKind($index, ['{', [T_EXTENDS]]);

        if (!$tokens[$extendsIndex]->isGivenKind(T_EXTENDS)) {
            return false;
        }

        if (Preg::match('/(?:Test|TestCase)$/', $tokens[$index]->getContent())) {
            return true;
        }

        while (null !== $index = $tokens->getNextMeaningfulToken($index)) {
            if ($tokens[$index]->equals('{')) {
                break; // end of class signature
            }

            if (!$tokens[$index]->isGivenKind(T_STRING)) {
                continue; // not part of extends nor part of implements; so continue
            }

            if (Preg::match('/(?:Test|TestCase)(?:Interface)?$/', $tokens[$index]->getContent())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an indices of PHPUnit classes in reverse appearance order.
     * Order is important - it's reverted, so if we inject tokens into collection,
     * we do it for bottom of file first, and then to the top of the file, so we
     * mitigate risk of not visiting whole collections (final indices).
     *
     * @return iterable<array{0: int, 1: int}> array of [int start, int end] indices from later to earlier classes
     */
    public function findPhpUnitClasses(Tokens $tokens): iterable
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS) || !$this->isPhpUnitClass($tokens, $index)) {
                continue;
            }

            $startIndex = $tokens->getNextTokenOfKind($index, ['{']);

            if (null === $startIndex) {
                return;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

            yield [$startIndex, $endIndex];
        }
    }
}
