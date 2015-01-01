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
class DuplicateSemicolonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->equals(';')) {
                continue;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);

            if (!$tokens[$prevIndex]->equals(';')) {
                continue;
            }

            $tokens->removeLeadingWhitespace($index);
            $token->clear();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove duplicated semicolons.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the BracesFixer, SpacesBeforeSemicolonFixer and MultilineSpacesBeforeSemicolonFixer
        return 10;
    }
}
