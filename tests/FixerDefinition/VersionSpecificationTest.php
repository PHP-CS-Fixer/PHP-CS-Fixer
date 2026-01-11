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

namespace PhpCsFixer\Tests\FixerDefinition;

use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\VersionSpecification
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class VersionSpecificationTest extends TestCase
{
    public function testConstructorRequiresEitherMinimumOrMaximum(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification();
    }

    /**
     * @dataProvider provideConstructorRejectsInvalidValuesCases
     *
     * @param null|int<1, max> $minimum
     * @param null|int<1, max> $maximum
     */
    public function testConstructorRejectsInvalidValues(?int $minimum = null, ?int $maximum = null): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification(
            $minimum,
            $maximum,
        );
    }

    /**
     * @return iterable<string, array{null|int, null|int}>
     */
    public static function provideConstructorRejectsInvalidValuesCases(): iterable
    {
        yield 'minimum is negative' => [-1, null];

        yield 'minimum is zero' => [0, null];

        yield 'maximum is negative' => [null, -1];

        yield 'maximum is zero' => [null, 0];

        yield 'maximum less than minimum' => [32, 31];
    }

    /**
     * @dataProvider provideIsSatisfiedByReturnsTrueCases
     *
     * @param null|int<1, max> $minimum
     * @param null|int<1, max> $maximum
     */
    public function testIsSatisfiedByReturnsTrue(?int $minimum, ?int $maximum, int $actual): void
    {
        $versionSpecification = new VersionSpecification(
            $minimum,
            $maximum,
        );

        self::assertTrue($versionSpecification->isSatisfiedBy($actual));
    }

    /**
     * @return iterable<string, array{null|int, null|int, int}>
     */
    public static function provideIsSatisfiedByReturnsTrueCases(): iterable
    {
        yield 'version-same-as-maximum' => [null, 100, 100];

        yield 'version-same-as-minimum' => [200, null, 200];

        yield 'version-between-minimum-and-maximum' => [299, 301, 300];

        yield 'version-same-as-minimum-and-maximum' => [400, 400, 400];
    }

    /**
     * @dataProvider provideIsSatisfiedByReturnsFalseCases
     *
     * @param null|int<1, max> $minimum
     * @param null|int<1, max> $maximum
     */
    public function testIsSatisfiedByReturnsFalse(?int $minimum, ?int $maximum, int $actual): void
    {
        $versionSpecification = new VersionSpecification(
            $minimum,
            $maximum,
        );

        self::assertFalse($versionSpecification->isSatisfiedBy($actual));
    }

    /**
     * @return iterable<string, array{null|int, null|int, int}>
     */
    public static function provideIsSatisfiedByReturnsFalseCases(): iterable
    {
        yield 'version-greater-than-maximum' => [null, 1_000, 1_001];

        yield 'version-less-than-minimum' => [2_000, null, 1_999];
    }
}
