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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\FixCommandExitStatusCalculator
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixCommandExitStatusCalculatorTest extends TestCase
{
    /**
     * @dataProvider provideCalculateCases
     */
    public function testCalculate(
        int $expected,
        bool $isDryRun,
        bool $hasChangedFiles,
        bool $hasInvalidErrors,
        bool $hasExceptionErrors,
        bool $hasLintErrorsAfterFixing
    ): void {
        $calculator = new FixCommandExitStatusCalculator();

        self::assertSame(
            $expected,
            $calculator->calculate(
                $isDryRun,
                $hasChangedFiles,
                $hasInvalidErrors,
                $hasExceptionErrors,
                $hasLintErrorsAfterFixing
            )
        );
    }

    /**
     * @return iterable<int, array{int, bool, bool, bool, bool, bool}>
     */
    public static function provideCalculateCases(): iterable
    {
        yield [0, true, false, false, false, false];

        yield [0, false, false, false, false, false];

        yield [8, true, true, false, false, false];

        yield [0, false, true, false, false, false];

        yield [4, true, false, true, false, false];

        yield [0, false, false, true, false, false];

        yield [12, true, true, true, false, false];

        yield [0, false, true, true, false, false];

        yield [76, true, true, true, true, false];

        yield [64, false, false, false, false, true];

        yield [64, false, false, false, true, false];

        yield [64, false, false, false, true, true];

        yield [8 | 64, true, true, false, true, true];
    }
}
