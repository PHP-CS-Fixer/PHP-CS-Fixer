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

use PhpCsFixer\Differ\DiffConsoleFormatter;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class TextReporter implements ReporterInterface
{
    public function getFormat(): string
    {
        return 'txt';
    }

    public function generate(ReportSummary $reportSummary): string
    {
        $output = '';

        $identifiedFiles = 0;
        foreach ($reportSummary->getChanged() as $file => $fixResult) {
            ++$identifiedFiles;
            $output .= sprintf('%4d) %s', $identifiedFiles, $file);

            if ($reportSummary->shouldAddAppliedFixers()) {
                $output .= $this->getAppliedFixers(
                    $reportSummary->getVerbosity(),
                    $reportSummary->isDecoratedOutput(),
                    $fixResult['appliedFixers'],
                    $fixResult['extraInfoFixers']
                );
            }

            $output .= $this->getDiff($reportSummary->isDecoratedOutput(), $fixResult['diff']);
            $output .= PHP_EOL;
        }

        return $output.$this->getFooter(
            $reportSummary->getTime(),
            $identifiedFiles,
            $reportSummary->getFilesCount(),
            $reportSummary->getMemory(),
            $reportSummary->isDryRun()
        );
    }

    /**
     * @param list<string> $appliedFixers
     */
    private function getAppliedFixers(int $verbosity, bool $isDecoratedOutput, array $appliedFixers, array $extraInfoFixers = []): string
    {
        if (!isset($extraInfoFixers['helpUri']) || $verbosity < OutputInterface::VERBOSITY_VERY_VERBOSE) {
            return sprintf(
                $isDecoratedOutput ? ' (<comment>%s</comment>)' : ' (%s)',
                implode(', ', $appliedFixers)
            );
        }

        $fixers = [];

        foreach ($appliedFixers as $appliedFixer) {
            $url = $extraInfoFixers['helpUri'][$appliedFixer] ?? '';
            if ($isDecoratedOutput && '' !== $url) {
                $fixers[] = sprintf('<href=%s;fg=yellow>%s</>', OutputFormatter::escape($url), $appliedFixer);
            } else {
                $fixers[] = $appliedFixer;
            }
        }

        return sprintf(
            ' (<comment>%s</comment>)',
            implode(', ', $fixers)
        );
    }

    private function getDiff(bool $isDecoratedOutput, string $diff): string
    {
        if ('' === $diff) {
            return '';
        }

        $diffFormatter = new DiffConsoleFormatter($isDecoratedOutput, sprintf(
            '<comment>      ---------- begin diff ----------</comment>%s%%s%s<comment>      ----------- end diff -----------</comment>',
            PHP_EOL,
            PHP_EOL
        ));

        return PHP_EOL.$diffFormatter->format($diff).PHP_EOL;
    }

    private function getFooter(int $time, int $identifiedFiles, int $files, int $memory, bool $isDryRun): string
    {
        if (0 === $time || 0 === $memory) {
            return '';
        }

        return PHP_EOL.sprintf(
            '%s %d of %d %s in %.3f seconds, %.3f MB memory used'.PHP_EOL,
            $isDryRun ? 'Found' : 'Fixed',
            $identifiedFiles,
            $files,
            $isDryRun ? 'files that can be fixed' : 'files',
            $time / 1_000,
            $memory / 1_024 / 1_024
        );
    }
}
