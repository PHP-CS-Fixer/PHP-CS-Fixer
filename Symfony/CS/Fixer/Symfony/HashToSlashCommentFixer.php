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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

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
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($i = 0, $count = count($tokens); $i < $count - 1; ++$i) {
            if ($tokens[$i]->isGivenKind(T_COMMENT) && '#' === substr($tokens[$i]->getContent(), 0, 1)) {
                $tokens[$i]->setContent('//'.substr($tokens[$i]->getContent(), 1));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Single line comments should use double slashes (//) and not hash (#).';
    }
}
