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
use Symfony\CS\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseConstantsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isNativeConstant()) {
                if (
                    $tokens[$tokens->getPrevNonWhitespace($index, array('whitespaces' => " \t\n"))]->isArray()
                    ||
                    $tokens[$tokens->getNextNonWhitespace($index, array('whitespaces' => " \t\n"))]->isArray()
                ) {
                    continue;
                }

                $token->content = strtolower($token->content);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The PHP constants true, false, and null MUST be in lower case.';
    }
}
