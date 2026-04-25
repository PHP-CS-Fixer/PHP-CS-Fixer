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
use PhpCsFixer\Documentation\DocumentationLocator;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use SebastianBergmann\Diff\Chunk;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Generates a report according to gitlabs subset of codeclimate json files.
 *
 * @author Hans-Christian Otto <c.otto@suora.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
    private DocumentationLocator $documentationLocator;
    private FixerFactory $fixerFactory;

    /**
     * @var array<string, FixerInterface>
     */
    private array $fixers;

    public function __construct()
    {
        $this->diffParser = new Parser();
        $this->documentationLocator = new DocumentationLocator();

        $this->fixerFactory = new FixerFactory();
        $this->fixerFactory->registerBuiltInFixers();

        $this->fixers = $this->createFixers();
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
            $fixerDiffs = $change['fixerDiffs'] ?? null;

            foreach ($change['appliedFixers'] as $fixerName) {
                $fixer = $this->fixers[$fixerName] ?? null;
                $description = null !== $fixer
                    ? $fixer->getDefinition()->getSummary()
                    : 'PHP-CS-Fixer.'.$fixerName.' (custom rule)';
                $body = \sprintf(
                    "%s\n%s",
                    $about,
                    null !== $fixer
                        ? \sprintf(
                            'Check [docs](https://cs.symfony.com/doc/rules/%s.html) for more information.',
                            substr($this->documentationLocator->getFixerDocumentationFileRelativePath($fixer), 0, -4), // -4 to drop `.rst`
                        )
                        : 'Check performed with a custom rule.',
                );

                // Prefer per-fixer diff (one entry per chunk inside that fixer's diff).
                // Fall back to the combined diff when per-fixer attribution is unavailable
                // (e.g. legacy callers passing the older shape, or `NullDiffer`).
                $diffToParse = $fixerDiffs[$fixerName] ?? $change['diff'];
                /** @var list<Diff> $parsedDiffs */
                $parsedDiffs = array_values($this->diffParser->parse($diffToParse));
                $lineRanges = self::getAllLineRanges($parsedDiffs);

                foreach ($lineRanges as $lines) {
                    $report[] = [
                        'check_name' => 'PHP-CS-Fixer.'.$fixerName,
                        'description' => $description,
                        'content' => ['body' => $body],
                        'categories' => ['Style'],
                        // Include line range in the fingerprint so multiple chunks for the
                        // same fixer × file remain distinguishable to consumers.
                        'fingerprint' => md5($fileName.$fixerName.$lines['begin'].'-'.$lines['end']),
                        'severity' => 'minor',
                        'location' => [
                            'path' => $fileName,
                            'lines' => $lines,
                        ],
                    ];
                }
            }
        }

        $jsonString = json_encode($report, \JSON_THROW_ON_ERROR);

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($jsonString) : $jsonString;
    }

    /**
     * Returns one `{begin, end}` range per chunk across all parsed diffs.
     * Falls back to a single `{begin: 0, end: 0}` when there are no chunks at all
     * (preserves the original behaviour for empty diffs / `NullDiffer`).
     *
     * @param list<Diff> $diffs
     *
     * @return non-empty-list<array{begin: int, end: int}>
     */
    private static function getAllLineRanges(array $diffs): array
    {
        $ranges = [];

        foreach ($diffs as $diff) {
            $chunks = \Closure::bind(static fn (Diff $diff): array => $diff->chunks, null, $diff)($diff);
            foreach ($chunks as $chunk) {
                $ranges[] = self::getBeginEndForDiffChunk($chunk);
            }
        }

        return [] !== $ranges ? $ranges : [['begin' => 0, 'end' => 0]];
    }

    /**
     * @return array{begin: int, end: int}
     */
    private static function getBeginEndForDiffChunk(Chunk $chunk): array
    {
        $start = \Closure::bind(static fn (Chunk $chunk): int => $chunk->start, null, $chunk)($chunk);
        $startRange = \Closure::bind(static fn (Chunk $chunk): int => $chunk->startRange, null, $chunk)($chunk);
        $lines = \Closure::bind(static fn (Chunk $chunk): array => $chunk->lines, null, $chunk)($chunk);

        \assert(\count($lines) > 0);

        $firstModifiedLineOffset = array_find_key($lines, static function (Line $line): bool {
            $type = \Closure::bind(static fn (Line $line): int => $line->type, null, $line)($line);

            return Line::UNCHANGED !== $type;
        });
        \assert(\is_int($firstModifiedLineOffset));

        return [
            // offset the start by where the first line is actually modified
            'begin' => $start + $firstModifiedLineOffset,
            // it's not where last modification takes place, only where diff (with --context) ends
            'end' => $start + $startRange,
        ];
    }

    /**
     * @return array<string, FixerInterface>
     */
    private function createFixers(): array
    {
        $fixers = [];

        foreach ($this->fixerFactory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        ksort($fixers);

        return $fixers;
    }
}
