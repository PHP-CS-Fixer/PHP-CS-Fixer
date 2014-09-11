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
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class PhpClosingTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $kinds = $tokens->findGivenKind(array(T_OPEN_TAG, T_CLOSE_TAG, T_INLINE_HTML));

        // leave code intact if there is:
        // - any T_INLINE_HTML code
        // - several opening tags
        if (count($kinds[T_INLINE_HTML]) || count($kinds[T_OPEN_TAG]) > 1) {
            return $content;
        }

        foreach (array_reverse($kinds[T_CLOSE_TAG], true) as $index => $token) {
            $tokens->removeLeadingWhitespace($index);
            $token->clear();

            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];

            if (null !== $prevToken->id || ';' !== $prevToken->content) {
                $tokens->insertAt($prevIndex + 1, new Token(';'));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the ShortTagFixer
        return 5;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The closing ?> tag MUST be omitted from files containing only PHP.';
    }
}
