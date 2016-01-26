<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BlankLineBeforeReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_RETURN);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_RETURN)) {
                continue;
            }

            $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

            if (!$prevNonWhitespaceToken->equalsAny(array(';', '}'))) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            if ($prevToken->isWhitespace()) {
                $parts = explode("\n", $prevToken->getContent());
                $countParts = count($parts);

                if (1 === $countParts) {
                    $prevToken->setContent(rtrim($prevToken->getContent(), " \t")."\n\n");
                } elseif (count($parts) <= 2) {
                    $prevToken->setContent("\n".$prevToken->getContent());
                }
            } else {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n\n")));

                ++$index;
                ++$limit;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'An empty line feed should precede a return statement.';
    }
}
