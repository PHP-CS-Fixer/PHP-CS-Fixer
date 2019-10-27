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

namespace PhpCsFixer\Report;

use Symfony\Component\Console\Formatter\OutputFormatter;

class RawFileOutputReporter implements ReporterInterface
{
    public const NAME = 'raw';

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary)
    {
        $changedFiles = $reportSummary->getChanged();
        if (1 !== \count($changedFiles)) {
            return '';
        }

        if ('php://stdin' !== array_key_first($changedFiles)) {
            throw new \RuntimeException('The raw format is allowed only while using with stdin.');
        }

        if (!isset($changedFiles['php://stdin']['diff'])) {
            throw new \RuntimeException('The raw format can be used only with --diff option.');
        }

        $report = (string) $changedFiles['php://stdin']['diff'];

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($report) : $report;
    }
}
