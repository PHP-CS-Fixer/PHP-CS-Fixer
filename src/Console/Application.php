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

namespace PhpCsFixer\Console;

use PhpCsFixer\Console\Command\CheckCommand;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Console\Command\ListFilesCommand;
use PhpCsFixer\Console\Command\ListSetsCommand;
use PhpCsFixer\Console\Command\SelfUpdateCommand;
use PhpCsFixer\Console\Command\WorkerCommand;
use PhpCsFixer\Console\SelfUpdate\GithubClient;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\PharChecker;
use PhpCsFixer\Runner\Parallel\WorkerException;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Application extends BaseApplication
{
    public const NAME = 'PHP CS Fixer';
    public const VERSION = '3.68.6-DEV';
    public const VERSION_CODENAME = 'Persian Successor';

    /**
     * @readonly
     */
    private ToolInfo $toolInfo;
    private ?Command $executedCommand = null;

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->toolInfo = new ToolInfo();

        // in alphabetical order
        $this->add(new DescribeCommand());
        $this->add(new CheckCommand($this->toolInfo));
        $this->add(new FixCommand($this->toolInfo));
        $this->add(new ListFilesCommand($this->toolInfo));
        $this->add(new ListSetsCommand());
        $this->add(new SelfUpdateCommand(
            new NewVersionChecker(new GithubClient()),
            $this->toolInfo,
            new PharChecker()
        ));
        $this->add(new WorkerCommand($this->toolInfo));
    }

    public static function getMajorVersion(): int
    {
        return (int) explode('.', self::VERSION)[0];
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $stdErr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : ($input->hasParameterOption('--format', true) && 'txt' !== $input->getParameterOption('--format', null, true) ? null : $output);

        if (null !== $stdErr) {
            $warningsDetector = new WarningsDetector($this->toolInfo);
            $warningsDetector->detectOldVendor();
            $warningsDetector->detectOldMajor();
            $warnings = $warningsDetector->getWarnings();

            if (\count($warnings) > 0) {
                foreach ($warnings as $warning) {
                    $stdErr->writeln(\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', $warning));
                }
                $stdErr->writeln('');
            }
        }

        $result = parent::doRun($input, $output);

        if (
            null !== $stdErr
            && $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE
        ) {
            $triggeredDeprecations = Utils::getTriggeredDeprecations();

            if (\count($triggeredDeprecations) > 0) {
                $stdErr->writeln('');
                $stdErr->writeln($stdErr->isDecorated() ? '<bg=yellow;fg=black;>Detected deprecations in use:</>' : 'Detected deprecations in use:');
                foreach ($triggeredDeprecations as $deprecation) {
                    $stdErr->writeln(\sprintf('- %s', $deprecation));
                }
            }
        }

        return $result;
    }

    /**
     * @internal
     */
    public static function getAbout(bool $decorated = false): string
    {
        $longVersion = \sprintf('%s <info>%s</info>', self::NAME, self::VERSION);

        $commit = '@git-commit@';
        $versionCommit = '';

        if ('@'.'git-commit@' !== $commit) { /** @phpstan-ignore-line as `$commit` is replaced during phar building */
            $versionCommit = substr($commit, 0, 7);
        }

        $about = implode('', [
            $longVersion,
            $versionCommit ? \sprintf(' <info>(%s)</info>', $versionCommit) : '', // @phpstan-ignore-line to avoid `Ternary operator condition is always true|false.`
            self::VERSION_CODENAME ? \sprintf(' <info>%s</info>', self::VERSION_CODENAME) : '', // @phpstan-ignore-line to avoid `Ternary operator condition is always true|false.`
            ' by <comment>Fabien Potencier</comment>, <comment>Dariusz Ruminski</comment> and <comment>contributors</comment>.',
        ]);

        if (false === $decorated) {
            return strip_tags($about);
        }

        return $about;
    }

    /**
     * @internal
     */
    public static function getAboutWithRuntime(bool $decorated = false): string
    {
        $about = self::getAbout(true)."\nPHP runtime: <info>".PHP_VERSION.'</info>';
        if (false === $decorated) {
            return strip_tags($about);
        }

        return $about;
    }

    public function getLongVersion(): string
    {
        return self::getAboutWithRuntime(true);
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand()];
    }

    /**
     * @throws \Throwable
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        $this->executedCommand = $command;

        return parent::doRunCommand($command, $input, $output);
    }

    protected function doRenderThrowable(\Throwable $e, OutputInterface $output): void
    {
        // Since parallel analysis utilises child processes, and they have their own output,
        // we need to capture the output of the child process to determine it there was an exception.
        // Default render format is not machine-friendly, so we need to override it for `worker` command,
        // in order to be able to easily parse exception data for further displaying on main process' side.
        if ($this->executedCommand instanceof WorkerCommand) {
            $output->writeln(WorkerCommand::ERROR_PREFIX.json_encode(
                [
                    'class' => \get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                ]
            ));

            return;
        }

        parent::doRenderThrowable($e, $output);

        if ($output->isVeryVerbose() && $e instanceof WorkerException) {
            $output->writeln('<comment>Original trace from worker:</comment>');
            $output->writeln('');
            $output->writeln($e->getOriginalTraceAsString());
            $output->writeln('');
        }
    }
}
