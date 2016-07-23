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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConcatWithoutSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $whitespaces = array('whitespaces' => " \t");

        foreach ($tokens as $index => $token) {
            if ($token->equals('.')) {
                $previousNonWhiteIndex = $tokens->getPrevNonWhitespace($index);
                if (!$tokens[$previousNonWhiteIndex]->isGivenKind(T_LNUMBER) && false === strpos($tokens[$previousNonWhiteIndex]->getContent(), "\n")) {
                    $tokens->removeLeadingWhitespace($index, $whitespaces);
                }

                $nextNonWhiteIndex = $tokens->getNextNonWhitespace($index);
                if (!$tokens[$nextNonWhiteIndex]->isGivenKind(array(T_LNUMBER, T_COMMENT, T_DOC_COMMENT))) {
                    $tokens->removeTrailingWhitespace($index, $whitespaces);
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
        return 'Concatenation should be used without spaces.';
    }
}
