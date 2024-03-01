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

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Runner\RunnerConfig;
use PhpCsFixer\ToolInfo;
use React\ChildProcess\Process as ReactProcess;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Represents single process that is handled within parallel run.
 * Inspired by:
 *   - https://github.com/phpstan/phpstan-src/blob/9ce425bca5337039fb52c0acf96a20a2b8ace490/src/Parallel/Process.php
 *   - https://github.com/phpstan/phpstan-src/blob/1477e752b4b5893f323b6d2c43591e68b3d85003/src/Process/ProcessHelper.php.
 *
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
final class Process
{
    // Properties required for process instantiation
    private string $command;
    private LoopInterface $loop;
    private int $timeoutSeconds;

    // Properties required for process execution
    private ReactProcess $process;
    private ?WritableStreamInterface $in = null;

    /** @var false|resource */
    private $stdErr;

    /** @var false|resource */
    private $stdOut;

    /** @var callable(mixed[]): void */
    private $onData;

    /** @var callable(\Throwable): void */
    private $onError;

    private ?TimerInterface $timer = null;

    private function __construct(string $command, LoopInterface $loop, int $timeoutSeconds)
    {
        $this->command = $command;
        $this->loop = $loop;
        $this->timeoutSeconds = $timeoutSeconds;
    }

    public static function create(
        LoopInterface $loop,
        RunnerConfig $runnerConfig,
        ProcessIdentifier $identifier,
        int $serverPort
    ): self {
        $input = self::getArgvInput();
        $phpBinary = (new PhpExecutableFinder())->find(false);

        if (false === $phpBinary) {
            throw new ParallelisationException('Cannot find PHP executable.');
        }

        $commandArgs = [
            $phpBinary,
            escapeshellarg($_SERVER['argv'][0]),
            'worker',
            '--port',
            (string) $serverPort,
            '--identifier',
            escapeshellarg((string) $identifier),
        ];

        if ($runnerConfig->isDryRun()) {
            $commandArgs[] = '--dry-run';
        }

        if (filter_var($input->getOption('diff'), FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--diff';
        }

        foreach (['allow-risky', 'config', 'rules', 'using-cache', 'cache-file'] as $option) {
            $optionValue = $input->getOption($option);

            if (null !== $optionValue) {
                $commandArgs[] = "--{$option}";
                $commandArgs[] = escapeshellarg($optionValue);
            }
        }

        return new self(
            implode(' ', $commandArgs),
            $loop,
            $runnerConfig->getParallelConfig()->getProcessTimeout()
        );
    }

    /**
     * @param callable(mixed[] $json): void                  $onData  callback to be called when data is received from the parallelisation operator
     * @param callable(\Throwable $exception): void          $onError callback to be called when an exception occurs
     * @param callable(?int $exitCode, string $output): void $onExit  callback to be called when the process exits
     */
    public function start(callable $onData, callable $onError, callable $onExit): void
    {
        $this->stdOut = tmpfile();
        if (false === $this->stdOut) {
            throw new ParallelisationException('Failed creating temp file for stdOut.');
        }

        $this->stdErr = tmpfile();
        if (false === $this->stdErr) {
            throw new ParallelisationException('Failed creating temp file for stdErr.');
        }

        $this->onData = $onData;
        $this->onError = $onError;

        $this->process = new ReactProcess($this->command, null, null, [
            1 => $this->stdOut,
            2 => $this->stdErr,
        ]);
        $this->process->start($this->loop);
        $this->process->on('exit', function ($exitCode) use ($onExit): void {
            $this->cancelTimer();

            $output = '';
            rewind($this->stdOut);
            $stdOut = stream_get_contents($this->stdOut);
            if (\is_string($stdOut)) {
                $output .= $stdOut;
            }

            rewind($this->stdErr);
            $stdErr = stream_get_contents($this->stdErr);
            if (\is_string($stdErr)) {
                $output .= $stdErr;
            }

            $onExit($exitCode, $output);

            fclose($this->stdOut);
            fclose($this->stdErr);
        });
    }

    /**
     * Handles requests from parallelisation operator to its worker (spawned process).
     *
     * @param mixed[] $data
     */
    public function request(array $data): void
    {
        $this->cancelTimer(); // Configured process timeout actually means "chunk timeout" (each request resets timer)

        if (null === $this->in) {
            throw new ParallelisationException(
                'Process not connected with parallelisation operator, ensure `bindConnection()` was called'
            );
        }

        $this->in->write($data);
        $this->timer = $this->loop->addTimer($this->timeoutSeconds, function (): void {
            ($this->onError)(
                new \Exception(
                    sprintf(
                        'Child process timed out after %d seconds. Try making it longer using `ParallelConfig`.',
                        $this->timeoutSeconds
                    )
                )
            );
        });
    }

    public function quit(): void
    {
        $this->cancelTimer();
        if (!$this->process->isRunning()) {
            return;
        }

        foreach ($this->process->pipes as $pipe) {
            $pipe->close();
        }

        if (null === $this->in) {
            return;
        }

        $this->in->end();
    }

    public function bindConnection(ReadableStreamInterface $out, WritableStreamInterface $in): void
    {
        $this->in = $in;

        $in->on('error', function (\Throwable $error): void {
            ($this->onError)($error);
        });

        $out->on('data', function (array $json): void {
            $this->cancelTimer();

            // Pass everything to the parallelisation operator, it should decide how to handle the data
            ($this->onData)($json);
        });
        $out->on('error', function (\Throwable $error): void {
            ($this->onError)($error);
        });
    }

    /**
     * Probably we should pass the input from the fix/check command explicitly, so it does not have to be re-created,
     * but for now it's good enough to simulate it here. It works as expected and we don't need to refactor the full
     * path from the CLI command, through Runner, up to this class.
     */
    private static function getArgvInput(): ArgvInput
    {
        $fixCommand = new FixCommand(new ToolInfo());
        $application = new Application();
        $application->add($fixCommand);

        // In order to have full list of options supported by the command (e.g. `--verbose`)
        $fixCommand->mergeApplicationDefinition(false);

        return new ArgvInput(null, $fixCommand->getDefinition());
    }

    private function cancelTimer(): void
    {
        if (null === $this->timer) {
            return;
        }

        $this->loop->cancelTimer($this->timer);
        $this->timer = null;
    }
}
