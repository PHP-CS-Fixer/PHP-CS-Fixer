<?php

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
     * @param bool        $expected
     * @param string      $candidate
     * @param string      $target
     * @param null|string $exception
     *
     * @dataProvider provideTestFulfillsCases
     */
    public function testFulfills($expected, $candidate, $target, $exception = null)
    {
        if (null !== $exception) {
            $this->expectException($exception);
        }

        $this->assertSame(
            $expected,
            PhpUnitTargetVersion::fulfills($candidate, $target)
        );
    }

    public function provideTestFulfillsCases()
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
