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

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractLinesBeforeNamespaceFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class NoBlankLinesBeforeNamespaceFixer extends AbstractLinesBeforeNamespaceFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_NAMESPACE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $this->fixLinesBeforeNamespace($tokens, $index, 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should be no blank lines before a namespace declaration.';
    }
}
