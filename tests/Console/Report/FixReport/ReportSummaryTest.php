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

namespace PhpCsFixer\Tests\Console\Report\FixReport;

use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\ReportSummary
 */
final class ReportSummaryTest extends TestCase
{
    public function testReportSummary(): void
    {
        $changed = [
            'someFile.php' => [
                'appliedFixers' => ['some_fixer_name_here'],
                'diff' => 'this text is a diff ;)',
            ],
        ];
        $filesCount = 10;
        $time = time();
        $memory = 123456789;
        $addAppliedFixers = true;
        $isDryRun = true;
        $isDecoratedOutput = false;

        $reportSummary = new ReportSummary(
            $changed,
            $filesCount,
            $time,
            $memory,
            $addAppliedFixers,
            $isDryRun,
            $isDecoratedOutput
        );

        static::assertSame($changed, $reportSummary->getChanged());
        static::assertSame($filesCount, $reportSummary->getFilesCount());
        static::assertSame($time, $reportSummary->getTime());
        static::assertSame($memory, $reportSummary->getMemory());
        static::assertSame($addAppliedFixers, $reportSummary->shouldAddAppliedFixers());
        static::assertSame($isDryRun, $reportSummary->isDryRun());
        static::assertSame($isDecoratedOutput, $reportSummary->isDecoratedOutput());
    }
}
