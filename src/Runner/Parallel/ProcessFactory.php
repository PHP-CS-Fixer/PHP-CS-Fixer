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

use PhpCsFixer\Runner\RunnerConfig;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
final class ProcessFactory
{
    private InputInterface $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    public function create(
        LoopInterface $loop,
        RunnerConfig $runnerConfig,
        ProcessIdentifier $identifier,
        int $serverPort
    ): Process {
        $phpBinary = (new PhpExecutableFinder())->find(false);

        if (false === $phpBinary) {
            throw new ParallelisationException('Cannot find PHP executable.');
        }

        $commandArgs = [
            $phpBinary,
            escapeshellarg(realpath(__DIR__.'/../../../php-cs-fixer')),
            'worker',
            '--port',
            (string) $serverPort,
            '--identifier',
            escapeshellarg((string) $identifier),
        ];

        if ($runnerConfig->isDryRun()) {
            $commandArgs[] = '--dry-run';
        }

        if (filter_var($this->input->getOption('diff'), FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--diff';
        }

        foreach (['allow-risky', 'config', 'rules', 'using-cache', 'cache-file'] as $option) {
            $optionValue = $this->input->getOption($option);

            if (null !== $optionValue) {
                $commandArgs[] = "--{$option}";
                $commandArgs[] = escapeshellarg($optionValue);
            }
        }

        return new Process(
            implode(' ', $commandArgs),
            $loop,
            $runnerConfig->getParallelConfig()->getProcessTimeout()
        );
    }
}
