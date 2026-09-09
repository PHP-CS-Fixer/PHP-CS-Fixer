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

namespace PhpCsFixer\Config;

use PhpCsFixer\Future;

/**
 * EXPERIMENTAL: This feature is experimental and does not fall under the backward compatibility promise.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerAnnotationMode
{
    public const FORBIDDEN = 'forbidden';
    public const MATCHING = 'matching';
    public const ALL = 'all';

    private const LEGACY_ENV_VAR = 'PHP_CS_FIXER_IGNORE_MISMATCHED_RULES_EXCEPTIONS';

    private const VALUES = [
        self::FORBIDDEN,
        self::MATCHING,
        self::ALL,
    ];

    private function __construct() {}

    /**
     * @return non-empty-list<self::ALL|self::FORBIDDEN|self::MATCHING>
     */
    public static function all(): array
    {
        return self::VALUES;
    }

    /**
     * @return self::ALL|self::FORBIDDEN|self::MATCHING
     */
    public static function getDefault(): string
    {
        if (self::legacyEnvironmentVariableIsEnabled()) {
            Future::triggerDeprecation(new \RuntimeException(\sprintf(
                'Environment variable "%s" is deprecated; use Config::setFixerAnnotationMode() or --fixer-annotation-mode instead.',
                self::LEGACY_ENV_VAR,
            )));

            return self::ALL;
        }

        return self::MATCHING;
    }

    public static function legacyEnvironmentVariableIsEnabled(): bool
    {
        return true === filter_var(getenv(self::LEGACY_ENV_VAR), \FILTER_VALIDATE_BOOLEAN);
    }
}
