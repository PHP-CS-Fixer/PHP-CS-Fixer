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
class JoinFunctionFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_STRING) as $index => $token) {
            if ('join' !== $token->getContent()) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_NS_SEPARATOR, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            if ($nextToken->isGivenKind(array(T_DOUBLE_COLON, T_NS_SEPARATOR))) {
                continue;
            }

            $token->setContent('implode');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Implode function should be used instead of join function.';
    }
}
