<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Functions;

use Symfony\CS\Tokenizer\Tokens;

final class FunctionDefinitionUtil
{
    /**
     * Checks if function/method defined in the scope.
     *
     * @param string $functionName
     * @param Tokens $tokens
     *
     * @return bool
     */
    public static function isDefinedInScope($functionName, Tokens $tokens)
    {
        $definitionSequence = array(array(T_FUNCTION, 'function'), array(T_STRING, $functionName));

        $matchedDefinition = $tokens->findSequence($definitionSequence, 0, $tokens->count() - 1, false);

        return null !== $matchedDefinition;
    }
}
