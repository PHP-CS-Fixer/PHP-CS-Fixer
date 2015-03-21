<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.3.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TrailingSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // [Structure] Don't add trailing spaces at the end of non-blank lines
        $tokens->setCode(preg_replace('/(?<=\S)[ \t]+$/m', '', $tokens->generateCode()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove trailing whitespace at the end of non-blank lines.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 20;
    }
}
