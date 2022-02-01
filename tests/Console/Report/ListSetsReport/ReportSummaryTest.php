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

namespace PhpCsFixer\Tests\Console\Report\ListSetsReport;

use PhpCsFixer\Console\Report\ListSetsReport\ReportSummary;
use PhpCsFixer\RuleSet\Sets\PhpCsFixerSet;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListSetsReport\ReportSummary
 */
final class ReportSummaryTest extends TestCase
{
    public function testReportSummary(): void
    {
        $sets = [
            new PhpCsFixerSet(),
        ];
        $reportSummary = new ReportSummary(
            $sets
        );

        static::assertSame($sets, $reportSummary->getSets());
    }
}
