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

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Odín del Río <odin.drp@gmail.com>
 *
 * @internal
 *
 * @deprecated This is a God Class anti-pattern. Don't expand it. It is fine to use logic that is already here (that's why we don't trigger deprecation warnings), but over time logic should be moved to dedicated, single-responsibility classes.
 */
final class Utils
{
    /**
     * @var array<string, true>
     */
    private static array $deprecations = [];

    private function __construct()
    {
        // cannot create instance of util. class
    }

    /**
     * Converts a camel cased string to a snake cased string.
     */
    public static function camelCaseToUnderscore(string $string): string
    {
        return mb_strtolower(Preg::replace('/(?<!^)(?<!_)((?=[\p{Lu}][^\p{Lu}])|(?<![\p{Lu}])(?=[\p{Lu}]))/', '_', $string));
    }

    /**
     * Calculate the trailing whitespace.
     *
     * What we're doing here is grabbing everything after the final newline.
     */
    public static function calculateTrailingWhitespaceIndent(Token $token): string
    {
        if (!$token->isWhitespace()) {
            throw new \InvalidArgumentException(\sprintf('The given token must be whitespace, got "%s".', $token->getName()));
        }

        $str = strrchr(
            str_replace(["\r\n", "\r"], "\n", $token->getContent()),
            "\n"
        );

        if (false === $str) {
            return '';
        }

        return ltrim($str, "\n");
    }

    /**
     * Perform stable sorting using provided comparison function.
     *
     * Stability is ensured by using Schwartzian transform.
     *
     * @template T
     * @template R
     *
     * @param list<T>             $elements
     * @param callable(T): R      $getComparedValue a callable that takes a single element and returns the value to compare
     * @param callable(R, R): int $compareValues    a callable that compares two values
     *
     * @return list<T>
     */
    public static function stableSort(array $elements, callable $getComparedValue, callable $compareValues): array
    {
        array_walk($elements, static function (&$element, int $index) use ($getComparedValue): void {
            $element = [$element, $index, $getComparedValue($element)];
        });

        usort($elements, static function ($a, $b) use ($compareValues): int {
            $comparison = $compareValues($a[2], $b[2]);

            if (0 !== $comparison) {
                return $comparison;
            }

            return $a[1] <=> $b[1];
        });

        return array_map(static fn (array $item) => $item[0], $elements);
    }

    /**
     * Sort fixers by their priorities.
     *
     * @param list<FixerInterface> $fixers
     *
     * @return list<FixerInterface>
     */
    public static function sortFixers(array $fixers): array
    {
        // Schwartzian transform is used to improve the efficiency and avoid
        // `usort(): Array was modified by the user comparison function` warning for mocked objects.
        return self::stableSort(
            $fixers,
            static fn (FixerInterface $fixer): int => $fixer->getPriority(),
            static fn (int $a, int $b): int => $b <=> $a
        );
    }

    /**
     * Join names in natural language using specified wrapper (double quote by default).
     *
     * @param list<string> $names
     *
     * @throws \InvalidArgumentException
     */
    public static function naturalLanguageJoin(array $names, string $wrapper = '"', string $lastJoin = 'and'): string
    {
        if (0 === \count($names)) {
            throw new \InvalidArgumentException('Array of names cannot be empty.');
        }

        if (\strlen($wrapper) > 1) {
            throw new \InvalidArgumentException('Wrapper should be a single-char string or empty.');
        }

        $names = array_map(static fn (string $name): string => \sprintf('%2$s%1$s%2$s', $name, $wrapper), $names);

        $last = array_pop($names);

        if (\count($names) > 0) {
            return implode(', ', $names).' '.$lastJoin.' '.$last;
        }

        return $last;
    }

    /**
     * Join names in natural language wrapped in backticks, e.g. `a`, `b` and `c`.
     *
     * @param list<string> $names
     *
     * @throws \InvalidArgumentException
     */
    public static function naturalLanguageJoinWithBackticks(array $names, string $lastJoin = 'and'): string
    {
        return self::naturalLanguageJoin($names, '`', $lastJoin);
    }

    public static function isFutureModeEnabled(): bool
    {
        return filter_var(
            getenv('PHP_CS_FIXER_FUTURE_MODE'),
            \FILTER_VALIDATE_BOOL
        );
    }

    public static function triggerDeprecation(\Exception $futureException): void
    {
        if (self::isFutureModeEnabled()) {
            throw new \RuntimeException(
                'Your are using something deprecated, see previous exception. Aborting execution because `PHP_CS_FIXER_FUTURE_MODE` environment variable is set.',
                0,
                $futureException
            );
        }

        $message = $futureException->getMessage();

        self::$deprecations[$message] = true;
        @trigger_error($message, \E_USER_DEPRECATED);
    }

    /**
     * @return list<string>
     */
    public static function getTriggeredDeprecations(): array
    {
        $triggeredDeprecations = array_keys(self::$deprecations);
        sort($triggeredDeprecations);

        return $triggeredDeprecations;
    }

    public static function convertArrayTypeToList(string $type): string
    {
        $parts = explode('[]', $type);
        $count = \count($parts) - 1;

        return str_repeat('list<', $count).$parts[0].str_repeat('>', $count);
    }

    /**
     * @param mixed $value
     */
    public static function toString($value): string
    {
        return \is_array($value)
            ? self::arrayToString($value)
            : self::scalarToString($value);
    }

    /**
     * @param mixed $value
     */
    private static function scalarToString($value): string
    {
        $str = var_export($value, true);

        return Preg::replace('/\bNULL\b/', 'null', $str);
    }

    /**
     * @param array<array-key, mixed> $value
     */
    private static function arrayToString(array $value): string
    {
        if (0 === \count($value)) {
            return '[]';
        }

        $isHash = !array_is_list($value);
        $str = '[';

        foreach ($value as $k => $v) {
            if ($isHash) {
                $str .= self::scalarToString($k).' => ';
            }

            $str .= \is_array($v)
                ? self::arrayToString($v).', '
                : self::scalarToString($v).', ';
        }

        return substr($str, 0, -2).']';
    }
}
