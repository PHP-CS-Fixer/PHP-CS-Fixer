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
 */
final class VersionSpecificationTest extends TestCase
{
    public function testConstructorRequiresEitherMinimumOrMaximum(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification();
    }

    /**
     * @dataProvider provideInvalidVersionCases
     */
    public function testConstructorRejectsInvalidMinimum(int $minimum): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification($minimum);
    }

    /**
     * @dataProvider provideInvalidVersionCases
     */
    public function testConstructorRejectsInvalidMaximum(int $maximum): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification(
            \PHP_VERSION_ID,
            $maximum
        );
    }

    public static function provideInvalidVersionCases(): iterable
    {
        yield 'negative' => [-1];

        yield 'zero' => [0];
    }

    public function testConstructorRejectsMaximumLessThanMinimum(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VersionSpecification(
            \PHP_VERSION_ID,
            \PHP_VERSION_ID - 1
        );
    }

    /**
     * @dataProvider provideIsSatisfiedByReturnsTrueCases
     */
    public function testIsSatisfiedByReturnsTrue(?int $minimum, ?int $maximum, int $actual): void
    {
        $versionSpecification = new VersionSpecification(
            $minimum,
            $maximum
        );

        self::assertTrue($versionSpecification->isSatisfiedBy($actual));
    }

    public static function provideIsSatisfiedByReturnsTrueCases(): iterable
    {
        yield 'version-same-as-maximum' => [null, \PHP_VERSION_ID, \PHP_VERSION_ID];

        yield 'version-same-as-minimum' => [\PHP_VERSION_ID, null, \PHP_VERSION_ID];

        yield 'version-between-minimum-and-maximum' => [\PHP_VERSION_ID - 1, \PHP_VERSION_ID + 1, \PHP_VERSION_ID];

        yield 'version-same-as-minimum-and-maximum' => [\PHP_VERSION_ID, \PHP_VERSION_ID, \PHP_VERSION_ID];
    }

    /**
     * @dataProvider provideIsSatisfiedByReturnsFalseCases
     */
    public function testIsSatisfiedByReturnsFalse(?int $minimum, ?int $maximum, int $actual): void
    {
        $versionSpecification = new VersionSpecification(
            $minimum,
            $maximum
        );

        self::assertFalse($versionSpecification->isSatisfiedBy($actual));
    }

    public static function provideIsSatisfiedByReturnsFalseCases(): iterable
    {
        yield 'version-greater-than-maximum' => [null, \PHP_VERSION_ID, \PHP_VERSION_ID + 1];

        yield 'version-less-than-minimum' => [\PHP_VERSION_ID, null, \PHP_VERSION_ID - 1];
    }
}
