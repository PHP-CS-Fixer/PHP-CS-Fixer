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

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
abstract class AbstractPhpdocToTypeDeclarationFixer extends AbstractFixer
{
    /**
     * @var array<string, bool>
     */
    private static $syntaxValidationCache = [];

    final protected function isValidSyntax($code)
    {
        if (!isset(self::$syntaxValidationCache[$code])) {
            try {
                Tokens::fromCode($code);
                self::$syntaxValidationCache[$code] = true;
            } catch (\ParseError $e) {
                self::$syntaxValidationCache[$code] = false;
            }
        }

        return self::$syntaxValidationCache[$code];
    }
}
