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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoWhitespaceInBlankLineFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
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
            $lines = preg_split("/(\r\n|\n)/", $content);

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

                $token->setContent(implode($this->whitespacesConfig->getLineEnding(), $lines));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the NoUselessReturnFixer, NoEmptyPhpdocFixer and NoUselessElseFixer.
        return -19;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Remove trailing whitespace at the end of blank lines.';
    }
}
