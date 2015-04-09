<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LineAfterNamespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (T_NAMESPACE === $token->getId()) {
                $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));
                $semicolonToken = $tokens[$semicolonIndex];

                if (!$semicolonToken->equals(';') || !isset($tokens[$semicolonIndex + 1])) {
                    continue;
                }

                $nextToken = $tokens[$semicolonIndex + 1];

                if (!$nextToken->isWhitespace()) {
                    $tokens->insertAt($semicolonIndex + 1, new Token(array(T_WHITESPACE, "\n\n")));
                } else {
                    $nextToken->setContent("\n\n".ltrim($nextToken->getContent()));
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST be one blank line after the namespace declaration.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the UnusedUseFixer
        return -20;
    }
}
