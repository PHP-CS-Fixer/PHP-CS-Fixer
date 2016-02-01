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
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ObjectOperatorWithoutWhitespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OBJECT_OPERATOR);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // [Structure] there should not be space before or after T_OBJECT_OPERATOR
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            // clear whitespace before ->
            if ($tokens[$index - 1]->isWhitespace(" \t") && !$tokens[$index - 2]->isComment()) {
                $tokens[$index - 1]->clear();
            }

            // clear whitespace after ->
            if ($tokens[$index + 1]->isWhitespace(" \t") && !$tokens[$index + 2]->isComment()) {
                $tokens[$index + 1]->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be space before or after object T_OBJECT_OPERATOR.';
    }
}
