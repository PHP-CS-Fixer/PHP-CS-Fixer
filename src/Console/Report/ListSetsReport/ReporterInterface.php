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

namespace PhpCsFixer\Console\Report\ListSetsReport;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
interface ReporterInterface
{
    /**
     * @return string
     */
    public function getFormat();

    /**
     * Process changed files array. Returns generated report.
     *
     * @return string
     */
    public function generate(ReportSummary $reportSummary);
}
