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
     * @dataProvider provideFulfillsCases
     *
     * @param null|class-string<\Throwable> $exception
     */
    public function testFulfills(bool $expected, string $candidate, string $target, ?string $exception = null): void
    {
        if (null !== $exception) {
            $this->expectException($exception);
        }

        self::assertSame(
            $expected,
            PhpUnitTargetVersion::fulfills($candidate, $target)
        );
    }

    /**
     * @return iterable<int, array{0: bool, 1: string, 2: string, 3?: string}>
     */
    public static function provideFulfillsCases(): iterable
    {
        yield [true, PhpUnitTargetVersion::VERSION_NEWEST, PhpUnitTargetVersion::VERSION_5_6];

        yield [true, PhpUnitTargetVersion::VERSION_NEWEST, PhpUnitTargetVersion::VERSION_5_2];

        yield [true, PhpUnitTargetVersion::VERSION_5_6, PhpUnitTargetVersion::VERSION_5_6];

        yield [true, PhpUnitTargetVersion::VERSION_5_6, PhpUnitTargetVersion::VERSION_5_2];

        yield [true, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_2];

        yield [false, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_6];

        yield [false, PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_NEWEST, \LogicException::class];
    }
}
