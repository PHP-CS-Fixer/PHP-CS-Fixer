<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer is responsible for ensuring that a certain number of
 * lines prefix a namespace declaration.
 *
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
abstract class AbstractLinesBeforeNamespaceFixer extends AbstractFixer
{
    /**
     * Make sure the expected number of new lines prefix a namespace.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $expected
     */
    protected function fixLinesBeforeNamespace(Tokens $tokens, $index, $expected)
    {
        // if we've got a <?php, then subtracted the number of new lines it
        // contains from the expected number in the following whitespace
        if (isset($tokens[$index - 2])) {
            $opening = $tokens[$index - 2];
            if ($opening->isGivenKind(T_OPEN_TAG)) {
                $expected -= substr_count($opening->getContent(), "\n");
            }
        }

        $previous = $tokens[$index - 1];
        if ($previous->isWhitespace()) {
            $content = $previous->getContent();
            if (substr_count($content, "\n") !== $expected) {
                $previous->setContent(str_repeat("\n", $expected));
            }
        }
    }
}
