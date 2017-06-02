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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Remove extra whitespace buried inside of any SLOC.
 *
 * @author Tristan Strathearn <r3oath@gmail.com>
 */
final class NoExtraWhitespaceFixer extends AbstractFixer
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
        foreach ($tokens as $index => $token) {
            if ($token->isWhitespace(" \0\x0B")) {
                $space = $token->getContent();

                // Here we'll normalize all consecutive whitespace into a
                // single whitespace character.
                if (strlen($space) > 1) {
                    $token->setContent(' ');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove extra whitespace buried inside of any SLOC.';
    }

    public function getPriority()
    {
        // Make sure this is generally run before any other whitespace fixer.
        return 97;
    }
}
