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
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesBetweenOffsetFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array('[', CT_ARRAY_INDEX_CURLY_BRACE_OPEN));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(array('[', array(CT_ARRAY_INDEX_CURLY_BRACE_OPEN)))) {
                continue;
            }

            $prevNonWhitespaceIndex = $tokens->getPrevNonWhitespace($index);
            if ($tokens[$prevNonWhitespaceIndex]->isComment()) {
                continue;
            }

            $tokens->removeLeadingWhitespace($index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be spaces between the offset braces and its closest expressions.';
    }
}
