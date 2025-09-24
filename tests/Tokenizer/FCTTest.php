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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Tokenizer\FCT
 */
final class FCTTest extends TestCase
{
    public function testConstantsHaveUniqueValues(): void
    {
        $constants = (new \ReflectionClass(FCT::class))->getConstants();

        self::assertSame(array_unique($constants), $constants, 'Values of FCT::T_* constants must be unique.');
    }

    public function testConstantsHaveCorrectValues(): void
    {
        foreach ((new \ReflectionClass(FCT::class))->getReflectionConstants() as $constant) {
            if (\defined($constant->getName())) {
                self::assertSame(\constant($constant->getName()), $constant->getValue());
            } else {
                self::assertLessThan(0, $constant->getValue());
            }
        }
    }

    /**
     * @requires PHP 8.5
     */
    public function testHighestSupportedPhpVersionHaveOnlyPositiveValues(): void
    {
        foreach ((new \ReflectionClass(FCT::class))->getReflectionConstants() as $constant) {
            self::assertGreaterThan(0, $constant->getValue());
        }
    }

    /**
     * @requires PHP < 8.0
     */
    public function testLowestSupportedPhpVersionHaveOnlyNegativeValues(): void
    {
        foreach ((new \ReflectionClass(FCT::class))->getReflectionConstants() as $constant) {
            self::assertLessThan(0, $constant->getValue());
        }
    }
}
