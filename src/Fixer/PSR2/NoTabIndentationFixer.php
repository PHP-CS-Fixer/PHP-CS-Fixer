<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR2;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.4.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoTabIndentationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                $content = preg_replace('/^(?:(?<! ) {1,3})?\t/m', '\1    ', $token->getContent(), -1, $count);

                // Also check for more tabs.
                while ($count !== 0) {
                    $content = preg_replace('/^(\ +)?\t/m', '\1    ', $content, -1, $count);
                }

                $tokens[$index]->setContent($content);
                continue;
            }

            if ($token->isWhitespace()) {
                $tokens[$index]->setContent(preg_replace('/(?:(?<! ) {1,3})?\t/', '    ', $token->getContent()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Code MUST use an indent of 4 spaces, and MUST NOT use tabs for indenting.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }
}
