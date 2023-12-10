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
     * @param null|string[]         $matches
     * @param int-mask<0, 256, 512> $flags
     *
     * @throws PregException
     */
    public static function match(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): bool
    {
        $result = @preg_match(self::addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return 1 === $result;
        }

        $result = @preg_match(self::removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return 1 === $result;
        }

        throw self::newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, (array) $pattern);
    }

    /**
     * @param null|string[] $matches
     *
     * @throws PregException
     */
    public static function matchAll(string $pattern, string $subject, ?array &$matches = null, int $flags = PREG_PATTERN_ORDER, int $offset = 0): int
    {
        $result = @preg_match_all(self::addUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_match_all(self::removeUtf8Modifier($pattern), $subject, $matches, $flags, $offset);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw self::newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, (array) $pattern);
    }

    /**
     * @param string|string[] $subject
     *
     * @throws PregException
     */
    public static function replace(string $pattern, string $replacement, $subject, int $limit = -1, ?int &$count = null): string
    {
        $result = @preg_replace(self::addUtf8Modifier($pattern), $replacement, $subject, $limit, $count);
        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_replace(self::removeUtf8Modifier($pattern), $replacement, $subject, $limit, $count);
        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw self::newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, (array) $pattern);
    }

    /**
     * @throws PregException
     */
    public static function replaceCallback(string $pattern, callable $callback, string $subject, int $limit = -1, ?int &$count = null): string
    {
        $result = @preg_replace_callback(self::addUtf8Modifier($pattern), $callback, $subject, $limit, $count);
        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_replace_callback(self::removeUtf8Modifier($pattern), $callback, $subject, $limit, $count);
        if (null !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw self::newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, (array) $pattern);
    }

    /**
     * @return string[]
     *
     * @throws PregException
     */
    public static function split(string $pattern, string $subject, int $limit = -1, int $flags = 0): array
    {
        $result = @preg_split(self::addUtf8Modifier($pattern), $subject, $limit, $flags);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        $result = @preg_split(self::removeUtf8Modifier($pattern), $subject, $limit, $flags);
        if (false !== $result && PREG_NO_ERROR === preg_last_error()) {
            return $result;
        }

        throw self::newPregException(preg_last_error(), preg_last_error_msg(), __METHOD__, (array) $pattern);
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

        $delimiter = $pattern[0];

        $endDelimiterPosition = strrpos($pattern, $delimiter);

        return substr($pattern, 0, $endDelimiterPosition).str_replace('u', '', substr($pattern, $endDelimiterPosition));
    }

    /**
     * Create PregException.
     *
     * Create the generic PregException message and if possible due to finding
     * an invalid pattern, tell more about such kind of error in the message.
     *
     * @param string[] $patterns
     */
    private static function newPregException(int $error, string $errorMsg, string $method, array $patterns): PregException
    {
        foreach ($patterns as $pattern) {
            $last = error_get_last();
            $result = @preg_match($pattern, '');

            if (false !== $result) {
                continue;
            }

            $code = preg_last_error();
            $next = error_get_last();

            if ($last !== $next) {
                $message = sprintf(
                    '(code: %d) %s',
                    $code,
                    preg_replace('~preg_[a-z_]+[()]{2}: ~', '', $next['message'])
                );
            } else {
                $message = sprintf('(code: %d)', $code);
            }

            return new PregException(
                sprintf('%s(): Invalid PCRE pattern "%s": %s (version: %s)', $method, $pattern, $message, PCRE_VERSION),
                $code
            );
        }

        return new PregException(sprintf('Error occurred when calling %s: %s.', $method, $errorMsg), $error);
    }
}
