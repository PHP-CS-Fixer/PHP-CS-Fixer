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
 * @author Graham Campbell <graham@mineuk.com>
 */
class SpacesBeforeSemicolonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals(';')) {
                continue;
            }

            $previous = $tokens[$index - 1];

            if ($previous->isWhitespace(" \t") && !$tokens[$index - 2]->isComment()) {
                $previous->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Single-line whitespace before closing semicolon are prohibited.';
    }
}
