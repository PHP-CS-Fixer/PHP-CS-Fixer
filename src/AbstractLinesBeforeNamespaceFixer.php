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

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer is responsible for ensuring that a certain number of
 * lines prefix a namespace declaration.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
        for ($i = 1; $i <= 2; ++$i) {
            if (isset($tokens[$index - $i])) {
                $opening = $tokens[$index - $i];
                if ($opening->isGivenKind(T_OPEN_TAG)) {
                    $expected -= substr_count($opening->getContent(), "\n");
                    break;
                } elseif ($opening->isGivenKind(T_WHITESPACE) !== true) {
                    break;
                }
            }
        }
        $previousIndex = $index - 1;
        $previous = $tokens[$previousIndex];
        if ($previous->isWhitespace()) {
            if (0 === $expected) {
                $tokens->clearAt($previousIndex);
            } elseif (substr_count($previous->getContent(), "\n") !== $expected) {
                $tokens[$previousIndex] = new Token(array(T_WHITESPACE, str_repeat("\n", $expected)));
            }
        } elseif (0 < $expected && $previous->isGivenKind(T_OPEN_TAG)) {
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, str_repeat("\n", $expected))));
        }
    }
}
