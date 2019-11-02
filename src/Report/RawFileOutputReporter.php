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

use PhpCsFixer\FileReader;
use Symfony\Component\Console\Formatter\OutputFormatter;

class RawFileOutputReporter implements ReporterInterface
{
    const NAME = 'raw';

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
            if ($reportSummary->isFailed()) {
                return '';
            }

            $stdinContent = FileReader::createSingleton()->read('php://stdin');

            return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($stdinContent) : $stdinContent;
        }

        if (!isset($changedFiles['php://stdin'])) {
            throw new \RuntimeException('The raw format is allowed only while using with stdin.');
        }

        if (!isset($changedFiles['php://stdin']['diff'])) {
            throw new \RuntimeException('The raw format can be used only with --diff option.');
        }

        $report = (string) $changedFiles['php://stdin']['diff'];

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($report) : $report;
    }
}
