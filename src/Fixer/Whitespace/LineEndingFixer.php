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
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class LineEndingFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
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
        $ending = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $count = count($tokens); $index < $count; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                if ($tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_END_HEREDOC)) {
                    $token->setContent(preg_replace(
                        "#\r\n|\n#",
                        $ending,
                        $token->getContent()
                    ));
                }

                continue;
            }

            if ($token->isGivenKind(array(T_OPEN_TAG, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_START_HEREDOC))) {
                $token->setContent(preg_replace(
                    "#\r\n|\n#",
                    $ending,
                    $token->getContent()
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All PHP files must use same line ending.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }
}
