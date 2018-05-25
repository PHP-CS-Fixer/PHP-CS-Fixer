<?php

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
    public function isPhpUnitClass(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_CLASS)) {
            throw new \LogicException(sprintf('No T_CLASS at given index %d, got %s.', $index, $tokens[$index]->getName()));
        }

        $index = $tokens->getNextMeaningfulToken($index);
        if (0 !== Preg::match('/(?:Test|TestCase)$/', $tokens[$index]->getContent())) {
            return true;
        }

        do {
            $index = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$index]->equals('{')) {
                break; // end of class signature
            }

            if (!$tokens[$index]->isGivenKind(T_STRING)) {
                continue; // not part of extends nor part of implements; so continue
            }

            if (0 !== Preg::match('/(?:Test|TestCase)(?:Interface)?$/', $tokens[$index]->getContent())) {
                return true;
            }
        } while (true); // safe `true` as we will always hit an `{` after a T_CLASS

        return false;
    }

    /**
     * @param Tokens $tokens
     *
     * @return \Generator array of [int start, int end] indexes from sooner to later classes
     */
    public function findPhpUnitClasses(Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count() - 1; $index < $limit; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $classIndex = $index;
            $index = $tokens->getNextTokenOfKind($index, ['{']);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
            if ($this->isPhpUnitClass($tokens, $classIndex)) {
                yield [$index, $endIndex];
            }

            $index = $endIndex;
        }
    }
}
