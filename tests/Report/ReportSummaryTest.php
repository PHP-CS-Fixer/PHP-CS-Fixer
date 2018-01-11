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

namespace PhpCsFixer\Tests\Report;

use PhpCsFixer\Report\ReportSummary;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\ReportSummary
 */
final class ReportSummaryTest extends TestCase
{
    public function testReportSummary()
    {
        $changed = array('', 5);
        $time = time();
        $memory = 123456789;
        $addAppliedFixers = true;
        $isDryRun = true;
        $isDecoratedOutput = false;

        $reportSummary = new ReportSummary(
            $changed,
            $time,
            $memory,
            $addAppliedFixers,
            $isDryRun,
            $isDecoratedOutput
        );

        $this->assertSame($changed, $reportSummary->getChanged());
        $this->assertSame($time, $reportSummary->getTime());
        $this->assertSame($memory, $reportSummary->getMemory());
        $this->assertSame($addAppliedFixers, $reportSummary->shouldAddAppliedFixers());
        $this->assertSame($isDryRun, $reportSummary->isDryRun());
        $this->assertSame($isDecoratedOutput, $reportSummary->isDecoratedOutput());
    }
}
