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

use PhpCsFixer\Runner\Parallel\ParallelConfig;

/**
 * @author Greg Korba <greg@codito.dev>
 */
final class RunnerConfig
{
    private bool $isDryRun = false;
    private bool $stopOnViolation = false;
    private ParallelConfig $parallelConfig;

    public function __construct(bool $isDryRun, bool $stopOnViolation, ParallelConfig $parallelConfig)
    {
        $this->isDryRun = $isDryRun;
        $this->stopOnViolation = $stopOnViolation;
        $this->parallelConfig = $parallelConfig;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    public function shouldStopOnViolation(): bool
    {
        return $this->stopOnViolation;
    }

    public function getParallelConfig(): ParallelConfig
    {
        return $this->parallelConfig;
    }
}
