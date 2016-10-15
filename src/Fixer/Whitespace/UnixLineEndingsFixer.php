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

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author SpacePossum
 */
final class UnixLineEndingsFixer extends AbstractFixer
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
        for ($index = 0, $count = count($tokens); $index < $count; ++$index) {
            if ($tokens[$index]->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                if ("\r\n" === substr($tokens[$index]->getContent(), -2) && $tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_END_HEREDOC)) {
                    $tokens[$index]->setContent(substr($tokens[$index]->getContent(), 0, -2)."\n");
                }

                continue;
            }

            if (!$tokens[$index]->isGivenKind(array(T_OPEN_TAG, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_START_HEREDOC))) {
                continue;
            }

            $tokens[$index]->setContent(str_replace("\r\n", "\n", $tokens[$index]->getContent()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All PHP files must use the Unix LF line ending.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }
}
