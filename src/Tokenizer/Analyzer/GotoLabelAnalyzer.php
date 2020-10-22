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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class GotoLabelAnalyzer
{
    /**
     * @param int $index
     *
     * @return bool
     */
    public function belongsToGoToLabel(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->equals(':')) {
            return false;
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$prevMeaningfulTokenIndex]->isGivenKind(T_STRING)) {
            return false;
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulTokenIndex);

        return $tokens[$prevMeaningfulTokenIndex]->equalsAny([';', '{', '}', [T_OPEN_TAG]]);
    }
}
