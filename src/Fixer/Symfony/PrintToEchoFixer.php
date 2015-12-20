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
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class PrintToEchoFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_PRINT)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

            if (!$prevToken->equalsAny(array(';', '{', '}', array(T_OPEN_TAG)))) {
                continue;
            }

            $tokens->overrideAt($index, array(T_ECHO, 'echo'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts print language construct to echo if possible.';
    }
}
