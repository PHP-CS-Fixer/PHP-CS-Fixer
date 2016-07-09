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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OperatorsSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens->isBinaryOperator($index)) {
                continue;
            }

            // skip `declare(foo ==bar)`
            $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_STRING)) {
                $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                if ($tokens[$prevMeaningfulIndex]->equals('(')) {
                    $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                    if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_DECLARE)) {
                        continue;
                    }
                }
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            if (!$tokens[$index - 1]->isWhitespace()) {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Binary operators should be surrounded by at least one space.';
    }
}
