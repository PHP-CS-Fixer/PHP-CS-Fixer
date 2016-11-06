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

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author SpacePossum
 */
class LinefeedFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
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

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All PHP files must use the Unix LF (linefeed) line ending.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }
}
