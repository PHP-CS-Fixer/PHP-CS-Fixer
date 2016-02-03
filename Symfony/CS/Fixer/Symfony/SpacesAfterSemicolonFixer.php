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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class SpacesAfterSemicolonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = count($tokens) - 2; $index > 0; --$index) {
            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                if (!$tokens[$index + 1]->equalsAny(array(')', array(T_INLINE_HTML)))) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                }
            } elseif (
                isset($tokens[$index + 2])
                && !$tokens[$index + 1]->equals(array(T_WHITESPACE, ' '))
                && $tokens[$index + 1]->isWhitespace(array('whitespaces' => " \t"))
                && !$tokens[$index + 2]->isComment()
                && !$tokens[$index + 2]->equals(')')
            ) {
                $tokens[$index + 1]->setContent(' ');
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Fix whitespace after a semicolon.';
    }
}
