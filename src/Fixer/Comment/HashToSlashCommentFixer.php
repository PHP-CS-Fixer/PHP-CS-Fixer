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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Changes single comments prefixes '#' with '//'.
 *
 * @author SpacePossum
 */
final class HashToSlashCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 0, $count = count($tokens); $i < $count - 1; ++$i) {
            if ($tokens[$i]->isGivenKind(T_COMMENT) && '#' === substr($tokens[$i]->getContent(), 0, 1)) {
                $tokens[$i]->setContent('//'.substr($tokens[$i]->getContent(), 1));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Single line comments should use double slashes (//) and not hash (#).';
    }
}
