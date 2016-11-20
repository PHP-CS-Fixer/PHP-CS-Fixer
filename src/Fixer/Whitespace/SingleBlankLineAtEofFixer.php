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
use PhpCsFixer\WhitespacesFixerConfigAwareInterface;

/**
 * A file must always end with a line endings character.
 *
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleBlankLineAtEofFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
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

        if ($count && !$tokens[$count - 1]->isGivenKind(array(T_INLINE_HTML, T_CLOSE_TAG, T_OPEN_TAG))) {
            $tokens->ensureWhitespaceAtIndex($count - 1, 1, $this->whitespacesConfig->getLineEnding());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run last to be sure the file is properly formatted before it runs
        return -50;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'A file must always end with a single empty line feed.';
    }
}
