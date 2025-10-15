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

use PhpCsFixer\FixerFactory;
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

    /**
     * A map of fixer names to their instances to access rule definitions.
     *
     * @var array<string, FixerInterface>
     */
    private array $fixers = [];

    public function __construct()
    {
        $this->diffParser = new Parser();

        $this->registerFixers();
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
        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            foreach ($change['appliedFixers'] as $fixerName) {
                $description = $fixerName;

                if (isset($this->fixers[$fixerName])) {
                    $description = $this->fixers[$fixerName]?->getDefinition()?->getSummary() ?? $fixerName;
                }

                $report[] = [
                    'check_name' => 'PHP-CS-Fixer.'.$fixerName,
                    'description' => $description,
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

    /**
     * Register fixers to access rule definitions.
     */
    private function registerFixers()
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        foreach ($fixerFactory->getFixers() as $fixer) {
            $this->fixers[$fixer->getName()] = $fixer;
        }
    }
}
