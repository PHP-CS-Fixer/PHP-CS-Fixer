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
 */
final class FixCommandExitStatusCalculatorTest extends TestCase
{
    /**
     * @param int  $expected
     * @param bool $isDryRun
     * @param bool $hasChangedFiles
     * @param bool $hasInvalidErrors
     * @param bool $hasExceptionErrors
     *
     * @dataProvider provideCalculateCases
     */
    public function testCalculate($expected, $isDryRun, $hasChangedFiles, $hasInvalidErrors, $hasExceptionErrors)
    {
        $calculator = new FixCommandExitStatusCalculator();

        $this->assertSame(
            $expected,
            $calculator->calculate($isDryRun, $hasChangedFiles, $hasInvalidErrors, $hasExceptionErrors)
        );
    }

    public function provideCalculateCases()
    {
        return [
            [0, true, false, false, false],
            [0, false, false, false, false],
            [8, true, true, false, false],
            [0, false, true, false, false],
            [4, true, false, true, false],
            [0, false, false, true, false],
            [12, true, true, true, false],
            [0, false, true, true, false],
            [76, true, true, true, true],
        ];
    }
}
