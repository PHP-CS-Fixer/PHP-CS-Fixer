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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion
 */
final class PhpUnitTargetVersionTest extends TestCase
{
    /**
     * @dataProvider provideTestFulfillsCases
     */
    public function testFulfills(bool $expected, string $candidate, string $target, ?string $exception = null): void
    {
        if (null !== $exception) {
            $this->expectException($exception);
        }

        static::assertSame(
            $expected,
            PhpUnitTargetVersion::fulfills($candidate, $target)
        );
    }

    public function provideTestFulfillsCases(): array
    {
        return [
            [true, PhpUnitTargetVersion::VERSION_NEWEST, PhpUnitTargetVersion::VERSION_5_6],
            [true, PhpUnitTargetVersion::VERSION_NEWEST, PhpUnitTargetVersion::VERSION_5_2],
            [true, PhpUnitTargetVersion::VERSION_5_6, PhpUnitTargetVersion::VERSION_5_6],
            [true, PhpUnitTargetVersion::VERSION_5_6, PhpUnitTargetVersion::VERSION_5_2],
            [true, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_2],
            [false, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_6],
            [false, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_6],
            [false, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_NEWEST, \LogicException::class],
        ];
    }
}
