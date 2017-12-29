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

        $classNameIndex = $tokens->getNextMeaningfulToken($index);
        if (0 !== preg_match('/(?:Test|TestCase)$/', $tokens[$classNameIndex]->getContent())) {
            return true;
        }

        $braceIndex = $tokens->getNextTokenOfKind($index, ['{']);
        $maybeParentSubNameToken = $tokens[$tokens->getPrevMeaningfulToken($braceIndex)];

        if (
            $maybeParentSubNameToken->isGivenKind(T_STRING) &&
            0 !== preg_match('/(?:Test|TestCase)$/', $maybeParentSubNameToken->getContent())
        ) {
            return true;
        }

        return false;
    }
}
