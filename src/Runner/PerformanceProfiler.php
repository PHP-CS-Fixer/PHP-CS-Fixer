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

namespace PhpCsFixer\Runner;

/**
 * Profiles performance of various stages in file processing.
 *
 * Tracks timing for:
 * - File reading
 * - Token parsing/generation
 * - Fixer application (per fixer and total)
 * - Linting
 * - Diff generation
 * - File writing
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PerformanceProfiler
{
    /**
     * @var array<string, float> Stage name => total microseconds
     */
    private array $timings = [];

    /**
     * @var array<string, array<string, float>> Per-fixer timings
     */
    private array $fixerTimings = [];

    /**
     * @var array<string, float|null> Stage name => start time in microseconds
     */
    private array $startTimes = [];

    public function startStage(string $stage): void
    {
        $this->startTimes[$stage] = microtime(true);
    }

    public function stopStage(string $stage): void
    {
        if (!isset($this->startTimes[$stage])) {
            return;
        }

        $elapsed = (microtime(true) - $this->startTimes[$stage]) * 1_000_000; // convert to microseconds
        $this->timings[$stage] = ($this->timings[$stage] ?? 0.0) + $elapsed;
        unset($this->startTimes[$stage]);
    }

    public function startFixerStage(string $fixerName): void
    {
        $this->startTimes["fixer:{$fixerName}"] = microtime(true);
    }

    public function stopFixerStage(string $fixerName): void
    {
        $key = "fixer:{$fixerName}";
        if (!isset($this->startTimes[$key])) {
            return;
        }

        $elapsed = (microtime(true) - $this->startTimes[$key]) * 1_000_000; // convert to microseconds
        if (!isset($this->fixerTimings[$fixerName])) {
            $this->fixerTimings[$fixerName] = ['total' => 0.0, 'count' => 0];
        }
        $this->fixerTimings[$fixerName]['total'] += $elapsed;
        ++$this->fixerTimings[$fixerName]['count'];
        unset($this->startTimes[$key]);
    }

    /**
     * @return array<string, float> Stage timings in microseconds
     */
    public function getTimings(): array
    {
        return $this->timings;
    }

    /**
     * @return array<string, array{total: float, count: int}> Per-fixer timings
     */
    public function getFixerTimings(): array
    {
        return $this->fixerTimings;
    }

    /**
     * Get timing for a specific stage in milliseconds.
     */
    public function getStageTimeMs(string $stage): float
    {
        return ($this->timings[$stage] ?? 0.0) / 1_000.0;
    }

    /**
     * Get average timing for a specific fixer in milliseconds.
     */
    public function getFixerAverageTimeMs(string $fixerName): float
    {
        if (!isset($this->fixerTimings[$fixerName])) {
            return 0.0;
        }

        return ($this->fixerTimings[$fixerName]['total'] / max(1, $this->fixerTimings[$fixerName]['count'])) / 1_000.0;
    }

    /**
     * Get total timing for a specific fixer in milliseconds.
     */
    public function getFixerTotalTimeMs(string $fixerName): float
    {
        return ($this->fixerTimings[$fixerName]['total'] ?? 0.0) / 1_000.0;
    }

    /**
     * Reset all timings.
     */
    public function reset(): void
    {
        $this->timings = [];
        $this->fixerTimings = [];
        $this->startTimes = [];
    }

    /**
     * Get a detailed report of all timings.
     */
    public function getReport(): string
    {
        $report = "=== Performance Profile Report ===\n\n";

        // Stage timings
        if ([] !== $this->timings) {
            $report .= "Stage Timings:\n";
            foreach ($this->timings as $stage => $microseconds) {
                $ms = $microseconds / 1_000.0;
                $report .= "  {$stage}: {$ms:.3f}ms\n";
            }
            $report .= "\n";
        }

        // Per-fixer timings
        if ([] !== $this->fixerTimings) {
            $report .= "Per-Fixer Timings:\n";
            $sortedFixers = $this->fixerTimings;
            usort($sortedFixers, static function (array $a, array $b): int {
                return (int) ($b['total'] <=> $a['total']);
            });

            foreach ($this->fixerTimings as $fixer => $data) {
                $avgMs = ($data['total'] / max(1, $data['count'])) / 1_000.0;
                $totalMs = $data['total'] / 1_000.0;
                $count = $data['count'];
                $report .= "  {$fixer}: {$totalMs:.3f}ms total ({$count} calls, {$avgMs:.3f}ms avg)\n";
            }
        }

        return $report;
    }
}
