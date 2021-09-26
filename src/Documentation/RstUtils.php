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

namespace PhpCsFixer\Documentation;

use PhpCsFixer\Preg;

/**
 * @internal
 */
final class RstUtils
{
    private function __construct()
    {
        // cannot create instance of util. class
    }

    public static function toRst(string $string, int $indent = 0): string
    {
        $string = wordwrap(Preg::replace('/(?<!`)(`.*?`)(?!`)/', '`$1`', $string), 80 - $indent);

        return 0 === $indent ? $string : self::indent($string, $indent);
    }

    public static function indent(string $string, int $indent): string
    {
        return Preg::replace('/(\n)(?!\n|$)/', '$1'.str_repeat(' ', $indent), $string);
    }
}
