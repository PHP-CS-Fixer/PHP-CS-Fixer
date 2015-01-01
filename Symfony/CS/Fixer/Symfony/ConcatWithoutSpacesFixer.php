<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConcatWithoutSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $whitespaces = array('whitespaces' => " \t");

        foreach ($tokens as $index => $token) {
            if ($token->equals('.')) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                    $tokens->removeLeadingWhitespace($index, $whitespaces);
                }

                if (!$tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                    $tokens->removeTrailingWhitespace($index, $whitespaces);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Concatenation should be used without spaces.';
    }
}
