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

namespace PhpCsFixer\Tests\Config;

use PhpCsFixer\Config\FixerAnnotationMode;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Config\FixerAnnotationMode
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(FixerAnnotationMode::class)]
final class FixerAnnotationModeTest extends TestCase
{
    private const LEGACY_ENV_VAR = 'PHP_CS_FIXER_IGNORE_MISMATCHED_RULES_EXCEPTIONS';

    public function testAll(): void
    {
        self::assertSame([
            FixerAnnotationMode::FORBIDDEN,
            FixerAnnotationMode::MATCHING,
            FixerAnnotationMode::ALL,
        ], FixerAnnotationMode::all());
    }

    public function testDefault(): void
    {
        $previousValue = getenv(self::LEGACY_ENV_VAR);
        putenv(self::LEGACY_ENV_VAR);

        try {
            self::assertSame(FixerAnnotationMode::MATCHING, FixerAnnotationMode::getDefault());
            self::assertFalse(FixerAnnotationMode::legacyEnvironmentVariableIsEnabled());
        } finally {
            putenv(false === $previousValue ? self::LEGACY_ENV_VAR : self::LEGACY_ENV_VAR.'='.$previousValue);
        }
    }

    public function testLegacyEnvironmentVariable(): void
    {
        $previousValue = getenv(self::LEGACY_ENV_VAR);
        putenv(self::LEGACY_ENV_VAR.'=1');

        $this->expectDeprecation(\sprintf(
            'Environment variable "%s" is deprecated; use Config::setFixerAnnotationMode() or --fixer-annotation-mode instead.',
            self::LEGACY_ENV_VAR,
        ));

        try {
            self::assertTrue(FixerAnnotationMode::legacyEnvironmentVariableIsEnabled());
            self::assertSame(FixerAnnotationMode::ALL, FixerAnnotationMode::getDefault());
        } finally {
            putenv(false === $previousValue ? self::LEGACY_ENV_VAR : self::LEGACY_ENV_VAR.'='.$previousValue);
        }
    }
}
