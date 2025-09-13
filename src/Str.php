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

namespace PhpCsFixer;

/**
 * @author Gregor Harlan
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class Str
{
    private function __construct()
    {
        // cannot create instance
    }

    public static function findFirst(string $string, string $search): int
    {
        $pos = strpos($string, $search);

        if (false === $pos) {
            throw new \InvalidArgumentException(\sprintf('The string "%s" was not found in "%s".', $search, $string));
        }

        return $pos;
    }

    public static function findLast(string $string, string $search): int
    {
        $pos = strrpos($string, $search);

        if (false === $pos) {
            throw new \InvalidArgumentException(\sprintf('The string "%s" was not found in "%s".', $search, $string));
        }

        return $pos;
    }

    public static function beforeFirst(string $string, string $search): string
    {
        return substr($string, 0, self::findFirst($string, $search));
    }

    public static function beforeLast(string $string, string $search): string
    {
        return substr($string, 0, self::findLast($string, $search));
    }

    public static function afterLast(string $string, string $search): string
    {
        return substr($string, self::findLast($string, $search) + \strlen($search));
    }

    public static function fromLast(string $string, string $search): string
    {
        return substr($string, self::findLast($string, $search));
    }
}
