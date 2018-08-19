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

/**
 * This class replaces preg_* functions to better handling UTF8 strings,
 * ensuring no matter "u" modifier is present or absent subject will be handled correctly.
 *
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class Preg
{
    /**
     * @param string        $pattern
     * @param string        $subject
     * @param null|string[] &$matches
     * @param int           $flags
     * @param int           $offset
     *
     * @throws PregException
     *
     * @return int
     */
    public static function match($pattern, $subject, &$matches = null, $flags = 0, $offset = 0)
    {
        $result = @preg_match(self::addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result) {
            return $result;
        }

        $result = @preg_match(self::removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result) {
            return $result;
        }

        throw new PregException('Error occurred when calling preg_match.', preg_last_error());
    }

    /**
     * @param string        $pattern
     * @param string        $subject
     * @param null|string[] &$matches
     * @param int           $flags
     * @param int           $offset
     *
     * @throws PregException
     *
     * @return int
     */
    public static function matchAll($pattern, $subject, &$matches = null, $flags = PREG_PATTERN_ORDER, $offset = 0)
    {
        $result = @preg_match_all(self::addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result) {
            return $result;
        }

        $result = @preg_match_all(self::removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result) {
            return $result;
        }

        throw new PregException('Error occurred when calling preg_match_all.', preg_last_error());
    }

    /**
     * @param string|string[] $pattern
     * @param string|string[] $replacement
     * @param string|string[] $subject
     * @param int             $limit
     * @param null|int        &$count
     *
     * @throws PregException
     *
     * @return string|string[]
     */
    public static function replace($pattern, $replacement, $subject, $limit = -1, &$count = null)
    {
        $result = @preg_replace(self::addUtf8Modifier($pattern), $replacement, $subject, $limit, $count);
        if (null !== $result) {
            return $result;
        }

        $result = @preg_replace(self::removeUtf8Modifier($pattern), $replacement, $subject, $limit, $count);
        if (null !== $result) {
            return $result;
        }

        throw new PregException('Error occurred when calling preg_replace.', preg_last_error());
    }

    /**
     * @param string|string[] $pattern
     * @param callable        $callback
     * @param string|string[] $subject
     * @param int             $limit
     * @param null|int        &$count
     *
     * @throws PregException
     *
     * @return string|string[]
     */
    public static function replaceCallback($pattern, $callback, $subject, $limit = -1, &$count = null)
    {
        $result = @preg_replace_callback(self::addUtf8Modifier($pattern), $callback, $subject, $limit, $count);
        if (null !== $result) {
            return $result;
        }

        $result = @preg_replace_callback(self::removeUtf8Modifier($pattern), $callback, $subject, $limit, $count);
        if (null !== $result) {
            return $result;
        }

        throw new PregException('Error occurred when calling preg_replace_callback.', preg_last_error());
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @param int    $limit
     * @param int    $flags
     *
     * @throws PregException
     *
     * @return string[]
     */
    public static function split($pattern, $subject, $limit = -1, $flags = 0)
    {
        $result = @preg_split(self::addUtf8Modifier($pattern), $subject, $limit, $flags);
        if (false !== $result) {
            return $result;
        }

        $result = @preg_split(self::removeUtf8Modifier($pattern), $subject, $limit, $flags);
        if (false !== $result) {
            return $result;
        }

        throw new PregException('Error occurred when calling preg_split.', preg_last_error());
    }

    /**
     * @param string|string[] $pattern
     *
     * @return string|string[]
     */
    private static function addUtf8Modifier($pattern)
    {
        if (\is_array($pattern)) {
            return array_map(__METHOD__, $pattern);
        }

        return $pattern.'u';
    }

    /**
     * @param string|string[] $pattern
     *
     * @return string|string[]
     */
    private static function removeUtf8Modifier($pattern)
    {
        if (\is_array($pattern)) {
            return array_map(__METHOD__, $pattern);
        }

        if ('' === $pattern) {
            return '';
        }

        $delimiter = substr($pattern, 0, 1);

        $endDelimiterPosition = strrpos($pattern, $delimiter);

        return substr($pattern, 0, $endDelimiterPosition).str_replace('u', '', substr($pattern, $endDelimiterPosition));
    }
}
