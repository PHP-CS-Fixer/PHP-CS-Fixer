<?php

/*
 * This file is part of the PHP CS utility.
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
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $whitespaces = array('whitespaces' => " \t");

        for ($index = count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equals('.')) {
                continue;
            }

            if ($tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                $tokens->ensureSingleWithSpaceAt($index + 1);
            } else {
                $tokens->removeTrailingWhitespace($index, $whitespaces);
            }

            if ($tokens[$tokens->getPrevNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
                $tokens->ensureSingleWithSpaceAt($index - 1);
            } elseif (!$tokens->isIndented($index)) {
                // remove leading white space but not when it is indenting
                $tokens->removeLeadingWhitespace($index, $whitespaces);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Concatenation should be used without spaces.';
    }
}
