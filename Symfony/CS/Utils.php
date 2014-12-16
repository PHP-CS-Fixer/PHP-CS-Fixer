<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
class Utils
{
    /**
     * Converts a camel cased string to an snake cased string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function camelCaseToUnderscore($string)
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/',
            function (array $matches) {
                return strtolower(strlen($matches[1]) ? $matches[1].'_'.$matches[2] : $matches[2]);
            },
            $string
        );
    }

    /**
     * Compare two integers for equality.
     *
     * We'll return 0 if they're equal, 1 if the first is bigger than the
     * second, and -1 if the second is bigger than the first.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public static function cmpInt($a, $b)
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    /**
     * Split a string up into an array of strings at the end of lines.
     *
     * This keeps relevant newline character at the end of these strings.
     *
     * @param string $content
     *
     * @return string[]
     */
    public static function splitLines($content)
    {
        preg_match_all("/[^\n\r]+[\r\n]*/", $content, $matches);

        return $matches[0];
    }

    /**
     * Count the number of newline characters in a string.
     *
     * @param string $content
     *
     * @return int
     */
    public static function countNewLines($content)
    {
        return substr_count($content, "\n") + substr_count($content, "\r");
    }

    /**
     * Calculate used indentation in whitespace.
     *
     * @param string $content
     *
     * @return string
     */
    public static function calculateIndent($content)
    {
        return ltrim(strrchr(str_replace(array("\r\n", "\r"), "\n", $content), 10), "\n");
    }
}
