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

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoWhitespaceInBlankLinesFixer extends AbstractFixer
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
            if (!$token->isWhitespace()) {
                continue;
            }

            $content = $token->getContent();
            $lines = preg_split("/([\r\n])/", $content);

            if (
                // fix T_WHITESPACES with at least 3 lines (eg `\n   \n`)
                count($lines) > 2
                // and T_WHITESPACES with at least 2 lines at the end of file
                || (count($lines) > 1 && !isset($tokens[$index + 1]))
            ) {
                $lMax = count($lines) - 1;
                if (!isset($tokens[$index + 1])) {
                    ++$lMax;
                }

                $lStart = 1;
                if (isset($tokens[$index - 1]) && $tokens[$index - 1]->isGivenKind(T_OPEN_TAG) && "\n" === substr($tokens[$index - 1]->getContent(), -1)) {
                    $lStart = 0;
                }

                for ($l = $lStart; $l < $lMax; ++$l) {
                    $lines[$l] = preg_replace('/^\h+$/', '', $lines[$l]);
                }

                $token->setContent(implode("\n", $lines));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove trailing whitespace at the end of blank lines.';
    }
}
