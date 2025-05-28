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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\FCT;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\FCT
 */
final class FCTTest extends TestCase
{
    private const EXPECTED_CONSTANTS = [
        'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
        'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
        'T_ATTRIBUTE',
        'T_ENUM',
        'T_MATCH',
        'T_NULLSAFE_OBJECT_OPERATOR',
        'T_PUBLIC_SET',
        'T_PROTECTED_SET',
        'T_PRIVATE_SET',
        'T_READONLY',
    ];

    public function testClassIsInternal(): void
    {
        self::assertSame(
            "/**\n     * @internal\n     */",
            (new \ReflectionClass(FCT::class))->getDocComment()
        );
    }

    public function testAllConstantsArePresentInEveryPhpVersionRuntime(): void
    {
        self::assertSame(
            self::EXPECTED_CONSTANTS,
            array_keys((new \ReflectionClass(FCT::class))->getConstants())
        );
    }

    public function testConstantsHaveUniqueValues(): void
    {
        $constants = (new \ReflectionClass(FCT::class))->getConstants();

        self::assertSame(array_unique($constants), $constants, 'Values of FCT::T_* constants must be unique.');
    }

    public function testConstantsHaveCorrectValues(): void
    {
        foreach (self::EXPECTED_CONSTANTS as $constantName) {
            $constant = new \ReflectionClassConstant(FCT::class, $constantName);
            if (\defined($constantName)) {
                self::assertSame(\constant($constantName), $constant->getValue());
            } else {
                self::assertLessThan(0, $constant->getValue());
            }
        }
    }

    /**
     * @requires PHP 8.4
     */
    public function testHighestSupportedPhpVersionHaveOnlyPositiveValues(): void
    {
        foreach (self::EXPECTED_CONSTANTS as $constantName) {
            $constant = new \ReflectionClassConstant(FCT::class, $constantName);
            self::assertGreaterThan(0, $constant->getValue());
        }
    }

    /**
     * @requires PHP < 8.0
     */
    public function testLowestSupportedPhpVersionHaveOnlyNegativeValues(): void
    {
        foreach (self::EXPECTED_CONSTANTS as $constantName) {
            $constant = new \ReflectionClassConstant(FCT::class, $constantName);
            self::assertLessThan(0, $constant->getValue());
        }
    }
}
