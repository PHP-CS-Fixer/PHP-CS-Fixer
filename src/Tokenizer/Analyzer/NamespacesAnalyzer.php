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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class NamespacesAnalyzer
{
    public function getDeclarations(Tokens $tokens)
    {
        $namespaces = [];

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $declarationEndIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
            $namespace = trim($tokens->generatePartialCode($index + 1, $declarationEndIndex - 1));
            $declarationParts = explode('\\', $namespace);
            $shortName = end($declarationParts);

            $namespaces[] = [
                'fullName' => $namespace,
                'shortName' => $shortName,
                'start' => $index,
                'end' => $declarationEndIndex,
            ];
        }

        return $namespaces;
    }
}
