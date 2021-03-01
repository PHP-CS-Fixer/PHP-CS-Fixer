<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tokenizer\Generator;

use PhpCsFixer\Tokenizer\Token;

/**
 * @internal
 */
final class NamespacedStringTokenGenerator
{
    /**
     * Parse a string that contains a namespace into tokens.
     *
     * @return Token[]
     */
    public function generate(string $input): array
    {
        $tokens = [];
        $parts = explode('\\', $input);

        foreach ($parts as $index => $part) {
            $tokens[] = new Token([T_STRING, $part]);
            if ($index !== \count($parts) - 1) {
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        return $tokens;
    }
}
