<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

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

            if (T_NAMESPACE === $token->id) {
                $semicolonIndex = null;
                $semicolonToken = $tokens->getNextTokenOfKind($index, array(';', '{'), $semicolonIndex);

                if (!$semicolonToken || ';' !== $semicolonToken->content || !isset($tokens[$semicolonIndex + 1])) {
                    continue;
                }

                $nextToken = $tokens[$semicolonIndex + 1];

                if (!$nextToken->isWhitespace()) {
                    $tokens->insertAt($semicolonIndex + 1, new Token(array(T_WHITESPACE, "\n\n")));
                } else {
                    $nextToken->content = "\n\n".ltrim($nextToken->content);
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
}
