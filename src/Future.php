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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class Future
{
    /**
     * @var array<string, true>
     */
    private static array $deprecations = [];

    private function __construct()
    {
        // cannot create instance
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
}
