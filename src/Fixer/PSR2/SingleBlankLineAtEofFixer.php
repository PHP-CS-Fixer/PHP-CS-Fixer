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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * A file must always end with a UNIX line endings character.
 *
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleBlankLineAtEofFixer extends AbstractFixer
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
        $count = $tokens->count();

        if (0 === $count) {
            return;
        }

        $token = $tokens[$count - 1];
        if ($token->isGivenKind(array(T_INLINE_HTML, T_CLOSE_TAG, T_OPEN_TAG))) {
            return;
        }

        if ($token->isWhitespace()) {
            $lineBreak = false === strrpos($token->getContent(), "\r") ? "\n" : "\r\n";
            $token->setContent($lineBreak);
        } else {
            $tokens->insertAt($count, new Token(array(T_WHITESPACE, "\n")));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A file must always end with a single empty line feed.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run last to be sure the file is properly formatted before it runs
        return -50;
    }
}
