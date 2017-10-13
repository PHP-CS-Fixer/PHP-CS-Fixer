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

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * Changes single comments prefixes '#' with '//'.
 *
 * @author SpacePossum
 *
 * @deprecated in 2.4, proxy to SingleLineCommentStyleFixer
 */
final class HashToSlashCommentFixer extends AbstractProxyFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            sprintf(
                'Single line comments should use double slashes `//` and not hash `#`. DEPRECATED: Use "%s" instead.',
                current($this->proxyFixers)->getName()
            ),
            [new CodeSample('<?php # comment')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        $fixer = new SingleLineCommentStyleFixer();
        $fixer->configure(['comment_types' => ['hash']]);

        return [$fixer];
    }
}
