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

namespace PhpCsFixer\Console\Report\FixReport;

use PhpCsFixer\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AiReporter implements ReporterInterface
{
    public function getFormat(): string
    {
        return 'ai';
    }

    public function generate(ReportSummary $reportSummary): string
    {
        $jsonFiles = [];

        foreach ($reportSummary->getChanged() as $file => $fixResult) {
            // always add 'applied_fixers', never add 'diff'
            $jsonFiles[] = [
                'file' => $file,
                'applied_fixers' => $fixResult['appliedFixers'],
            ];
        }

        $json = [
            'tool' => Application::NAME,
            'version' => Application::VERSION,
            'command' => $reportSummary->isDryRun() ? 'check' : 'fix',
            'result' => [] === $jsonFiles ? 'OK' : 'violations',
            'files_processed' => $reportSummary->getFilesCount(),
            'files_with_violations_count' => \count($jsonFiles),
            'files_with_violations' => $jsonFiles,
            'duration_s' => round($reportSummary->getTime() / 1_000, 3),
            'memory_mb' => round($reportSummary->getMemory() / 1_024 / 1_024, 3),
        ];

        $json = json_encode($json, \JSON_THROW_ON_ERROR);

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($json) : $json;
    }
}
