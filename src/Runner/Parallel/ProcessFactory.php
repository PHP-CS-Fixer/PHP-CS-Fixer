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
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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
        $commandArgs = $this->getCommandArgs($serverPort, $identifier, $runnerConfig);

        return new Process(
            implode(' ', $commandArgs),
            $loop,
            $runnerConfig->getParallelConfig()->getProcessTimeout()
        );
    }

    /**
     * @private
     *
     * @return non-empty-list<string>
     */
    public function getCommandArgs(int $serverPort, ProcessIdentifier $identifier, RunnerConfig $runnerConfig): array
    {
        $phpBinary = (new PhpExecutableFinder())->find(false);

        if (false === $phpBinary) {
            throw new ParallelisationException('Cannot find PHP executable.');
        }

        $mainScript = realpath(__DIR__.'/../../../php-cs-fixer');
        if (false === $mainScript
            && isset($_SERVER['argv'][0])
            && str_contains($_SERVER['argv'][0], 'php-cs-fixer')
        ) {
            $mainScript = $_SERVER['argv'][0];
        }

        if (!is_file($mainScript)) {
            throw new ParallelisationException('Cannot determine Fixer executable.');
        }

        $commandArgs = [
            escapeshellarg($phpBinary),
            escapeshellarg($mainScript),
            'worker',
            '--port',
            (string) $serverPort,
            '--identifier',
            escapeshellarg($identifier->toString()),
        ];

        if ($runnerConfig->isDryRun()) {
            $commandArgs[] = '--dry-run';
        }

        if (filter_var($this->input->getOption('diff'), \FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--diff';
        }

        if (filter_var($this->input->getOption('stop-on-violation'), \FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--stop-on-violation';
        }

        foreach (['allow-risky', 'config', 'rules', 'using-cache', 'cache-file'] as $option) {
            $optionValue = $this->input->getOption($option);

            if (null !== $optionValue) {
                $commandArgs[] = "--{$option}";
                $commandArgs[] = escapeshellarg($optionValue);
            }
        }

        return $commandArgs;
    }
}
