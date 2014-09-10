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
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_RETURN)) {
                continue;
            }

            $prevNonWhitespaceToken = $tokens->getPrevNonWhitespace($index);

            if (!$prevNonWhitespaceToken->equalsAny(array(';', '}'))) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            if ($prevToken->isWhitespace()) {
                $parts = explode("\n", $prevToken->content);
                $countParts = count($parts);

                if (1 === $countParts) {
                    $prevToken->content = rtrim($prevToken->content, " \t")."\n\n";
                } elseif (count($parts) <= 2) {
                    $prevToken->content = "\n".$prevToken->content;
                }
            } else {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n\n")));

                ++$index;
                ++$limit;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'An empty line feed should precede a return statement.';
    }
}
