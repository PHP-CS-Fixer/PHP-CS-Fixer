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
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EofEndingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] A file must always end with a linefeed character

        if (empty($content)) {
            return $content;
        }

        $tokens = Tokens::fromCode($content);
        $count = $tokens->count();
        if (0 === $count) {
            return;
        }

        $token = $tokens[$count - 1];
        switch ($token->getId()) {
            case T_CLOSE_TAG:
            case T_INLINE_HTML: {
                return $content;
            }
        }

        return rtrim($content)."\n";
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A PHP file must always end with a single empty line feed.';
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
