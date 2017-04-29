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

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Test\AccessibleObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\FixCommand
 */
final class FixCommandTest extends TestCase
{
    /**
     * @param int  $expected
     * @param bool $isDryRun
     * @param bool $hasChangedFiles
     * @param bool $hasInvalidErrors
     * @param bool $hasExceptionErrors
     *
     * @dataProvider provideCalculateExitStatusCases
     */
    public function testCalculateExitStatus($expected, $isDryRun, $hasChangedFiles, $hasInvalidErrors, $hasExceptionErrors)
    {
        $command = new AccessibleObject(new FixCommand());

        $this->assertSame(
            $expected,
            $command->calculateExitStatus($isDryRun, $hasChangedFiles, $hasInvalidErrors, $hasExceptionErrors)
        );
    }

    public function provideCalculateExitStatusCases()
    {
        return array(
            array(0, true, false, false, false),
            array(0, false, false, false, false),
            array(8, true, true, false, false),
            array(0, false, true, false, false),
            array(4, true, false, true, false),
            array(0, false, false, true, false),
            array(12, true, true, true, false),
            array(0, false, true, true, false),
            array(76, true, true, true, true),
        );
    }
}
