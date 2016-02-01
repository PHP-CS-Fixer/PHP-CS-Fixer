<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR2;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class UnixLineEndingsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // [Structure] Use the UNIX line ending character (0x0A) to end lines
        $tokens->setCode(str_replace("\r\n", "\n", $tokens->generateCode()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All PHP files must use the Unix LF line ending.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }
}
