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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ReportSummary
{
    private array $changed;

    private int $time;

    private int $memory;

    private bool $addAppliedFixers;

    private bool $isDryRun;

    private bool $isDecoratedOutput;

    /**
     * @param int $time   duration in milliseconds
     * @param int $memory memory usage in bytes
     */
    public function __construct(
        array $changed,
        int $time,
        int $memory,
        bool $addAppliedFixers,
        bool $isDryRun,
        bool $isDecoratedOutput
    ) {
        $this->changed = $changed;
        $this->time = $time;
        $this->memory = $memory;
        $this->addAppliedFixers = $addAppliedFixers;
        $this->isDryRun = $isDryRun;
        $this->isDecoratedOutput = $isDecoratedOutput;
    }

    public function isDecoratedOutput(): bool
    {
        return $this->isDecoratedOutput;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    public function getChanged(): array
    {
        return $this->changed;
    }

    public function getMemory(): int
    {
        return $this->memory;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function shouldAddAppliedFixers(): bool
    {
        return $this->addAppliedFixers;
    }
}
