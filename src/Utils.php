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
 */
final class Utils
{
    /**
     * @var array<string,true>
     */
    private static $deprecations = [];

    private function __construct()
    {
        // cannot create instance of util. class
    }

    /**
     * Converts a camel cased string to a snake cased string.
     */
    public static function camelCaseToUnderscore(string $string): string
    {
        return mb_strtolower(Preg::replace('/(?<!^)((?=[\p{Lu}][^\p{Lu}])|(?<![\p{Lu}])(?=[\p{Lu}]))/', '_', $string));
    }

    /**
     * Calculate the trailing whitespace.
     *
     * What we're doing here is grabbing everything after the final newline.
     */
    public static function calculateTrailingWhitespaceIndent(Token $token): string
    {
        if (!$token->isWhitespace()) {
            throw new \InvalidArgumentException(sprintf('The given token must be whitespace, got "%s".', $token->getName()));
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
     * @param mixed[]  $elements
     * @param callable $getComparedValue a callable that takes a single element and returns the value to compare
     * @param callable $compareValues    a callable that compares two values
     *
     * @return mixed[]
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

        return array_map(static function (array $item) {
            return $item[0];
        }, $elements);
    }

    /**
     * Sort fixers by their priorities.
     *
     * @param FixerInterface[] $fixers
     *
     * @return FixerInterface[]
     */
    public static function sortFixers(array $fixers): array
    {
        // Schwartzian transform is used to improve the efficiency and avoid
        // `usort(): Array was modified by the user comparison function` warning for mocked objects.
        return self::stableSort(
            $fixers,
            static function (FixerInterface $fixer): int {
                return $fixer->getPriority();
            },
            static function (int $a, int $b): int {
                return $b <=> $a;
            }
        );
    }

    /**
     * Join names in natural language wrapped in backticks, e.g. `a`, `b` and `c`.
     *
     * @param string[] $names
     *
     * @throws \InvalidArgumentException
     */
    public static function naturalLanguageJoinWithBackticks(array $names): string
    {
        if (0 === \count($names)) {
            throw new \InvalidArgumentException('Array of names cannot be empty.');
        }

        $names = array_map(static function (string $name): string {
            return sprintf('`%s`', $name);
        }, $names);

        $last = array_pop($names);

        if (\count($names) > 0) {
            return implode(', ', $names).' and '.$last;
        }

        return $last;
    }

    public static function triggerDeprecation(\Exception $futureException): void
    {
        if (getenv('PHP_CS_FIXER_FUTURE_MODE')) {
            throw new \RuntimeException(
                'Your are using something deprecated, see previous exception. Aborting execution because `PHP_CS_FIXER_FUTURE_MODE` environment variable is set.',
                0,
                $futureException
            );
        }

        $message = $futureException->getMessage();

        self::$deprecations[$message] = true;
        @trigger_error($message, E_USER_DEPRECATED);
    }

    public static function getTriggeredDeprecations(): array
    {
        $triggeredDeprecations = array_keys(self::$deprecations);
        sort($triggeredDeprecations);

        return $triggeredDeprecations;
    }
}
