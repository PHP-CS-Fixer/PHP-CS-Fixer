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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tokenizer\Token;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * @author SpacePossum
 */
final class TokenCaster
{
    public function castToken(Token $token, array $object, Stub $stub, $isNested)
    {
        $classPrefix = sprintf("\0%s\0", $stub->class);
        return array(
            $classPrefix.'id' => $token->getId(),
            $classPrefix.'content' => $token->getContent(),
            $classPrefix.'isArray' => $token->isArray(),
            'name' => $token->getName(),
            'isComment' => $token->isComment(),
            'isEmpty' => $token->isEmpty(), // (cleared)
            'isWhiteSpace' => $token->isWhitespace(),
        );
    }
}
