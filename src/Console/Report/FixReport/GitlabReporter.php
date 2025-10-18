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
use SebastianBergmann\Diff\Chunk;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Diff\Parser;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Generates a report according to gitlabs subset of codeclimate json files.
 *
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @see https://github.com/codeclimate/platform/blob/master/spec/analyzers/SPEC.md#data-types
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GitlabReporter implements ReporterInterface
{
    private Parser $diffParser;

    public function __construct()
    {
        $this->diffParser = new Parser();
    }

    public function getFormat(): string
    {
        return 'gitlab';
    }

    /**
     * Process changed files array. Returns generated report.
     */
    public function generate(ReportSummary $reportSummary): string
    {
        $about = Application::getAbout();

        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            foreach ($change['appliedFixers'] as $fixerName) {
                $fixer = $this->getFixers()[$fixerName] ?? null;

                if (isset($this->fixers[$fixerName])) {
                    $description = $this->fixers[$fixerName]?->getDefinition()?->getSummary() ?? $fixerName;
                }

                $report[] = [
                    'check_name' => 'PHP-CS-Fixer.'.$fixerName,
                    'description' => null !== $fixer
                        ? $fixer->getDefinition()->getSummary()
                        : 'PHP-CS-Fixer.'.$fixerName.' (custom rule)',
                    'categories' => ['Style'],
                    'fingerprint' => md5($fileName.$fixerName),
                    'severity' => 'minor',
                    'location' => [
                        'path' => $fileName,
                        'lines' => self::getLines($this->diffParser->parse($change['diff'])),
                    ],
                ];
            }
        }

        $jsonString = json_encode($report, \JSON_THROW_ON_ERROR);

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($jsonString) : $jsonString;
    }

    /**
     * @param list<Diff> $diffs
     *
     * @return array{begin: int, end: int}
     */
    private static function getLines(array $diffs): array
    {
        if (isset($diffs[0])) {
            $firstDiff = $diffs[0];

            $firstChunk = \Closure::bind(static fn (Diff $diff) => array_shift($diff->chunks), null, $firstDiff)($firstDiff);

            if ($firstChunk instanceof Chunk) {
                return \Closure::bind(static fn (Chunk $chunk): array => ['begin' => $chunk->start, 'end' => $chunk->startRange], null, $firstChunk)($firstChunk);
            }
        }

        return ['begin' => 0, 'end' => 0];
    }
}
