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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class PrintToEchoFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $printTokens = $tokens->findGivenKind(T_PRINT);

        foreach ($printTokens as $printIndex => $printToken) {
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($printIndex)];
            if (!$prevToken->equalsAny(array(';', '{', '}', array(T_OPEN_TAG)))) {
                continue;
            }

            $tokens->overrideAt($printIndex, array(T_ECHO, 'echo'));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts print language construct to echo if possible.';
    }
}
