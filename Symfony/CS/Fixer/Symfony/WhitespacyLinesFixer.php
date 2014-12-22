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
class WhitespacyLinesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $contentAfterRegex = preg_replace('/^\h+$/m', '', $content);

        $originalTokens = Tokens::fromCode($content);
        $newTokens = Tokens::fromCode($contentAfterRegex);

        foreach ($newTokens as $tokenIndex => $newToken) {
            if ($newToken->isGivenKind(array(T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING))) {
                $tokenBeforeRegex = $originalTokens[$tokenIndex];
                $newToken->setContent($tokenBeforeRegex->getContent());
            }
        }

        return $newTokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove trailing whitespace at the end of blank lines.';
    }
}
