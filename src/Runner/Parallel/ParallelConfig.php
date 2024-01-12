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

namespace PhpCsFixer\Runner\Parallel;

/**
 * @author Greg Korba <greg@codito.dev>
 */
final class ParallelConfig
{
    private int $filesPerProcess;
    private int $maxProcesses;
    private int $processTimeout;

    public function __construct(int $maxProcesses = 1, int $filesPerProcess = 10, int $processTimeout = 0)
    {
        $this->maxProcesses = $maxProcesses;
        $this->filesPerProcess = $filesPerProcess;
        $this->processTimeout = $processTimeout;
    }

    public function getFilesPerProcess(): int
    {
        return $this->filesPerProcess;
    }

    public function getMaxProcesses(): int
    {
        return $this->maxProcesses;
    }

    public function getProcessTimeout(): int
    {
        return $this->processTimeout;
    }

    /**
     * @TODO Automatic detection of available cores
     */
    public static function detect(): self
    {
        return new self();
    }
}
