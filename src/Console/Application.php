<?php

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

use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Console\Command\ListFilesCommand;
use PhpCsFixer\Console\Command\ListSetsCommand;
use PhpCsFixer\Console\Command\SelfUpdateCommand;
use PhpCsFixer\Console\SelfUpdate\GithubClient;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\PharChecker;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Application as BaseApplication;
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
    const VERSION = '2.19.3';
    const VERSION_CODENAME = 'Testament';

    /**
     * @var ToolInfo
     */
    private $toolInfo;

    public function __construct()
    {
        if (!getenv('PHP_CS_FIXER_FUTURE_MODE')) {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        }

        parent::__construct('PHP CS Fixer', self::VERSION);

        $this->toolInfo = new ToolInfo();

        // in alphabetical order
        $this->add(new DescribeCommand());
        $this->add(new FixCommand($this->toolInfo));
        $this->add(new ListFilesCommand($this->toolInfo));
        $this->add(new ListSetsCommand());
        $this->add(new SelfUpdateCommand(
            new NewVersionChecker(new GithubClient()),
            $this->toolInfo,
            new PharChecker()
        ));
    }

    /**
     * @return int
     */
    public static function getMajorVersion()
    {
        return (int) explode('.', self::VERSION)[0];
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $stdErr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : ($input->hasParameterOption('--format', true) && 'txt' !== $input->getParameterOption('--format', null, true) ? null : $output)
        ;

        if (null !== $stdErr) {
            $warningsDetector = new WarningsDetector($this->toolInfo);
            $warningsDetector->detectOldVendor();
            $warningsDetector->detectOldMajor();
            $warnings = $warningsDetector->getWarnings();

            if ($warnings) {
                foreach ($warnings as $warning) {
                    $stdErr->writeln(sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', $warning));
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
            if ($triggeredDeprecations) {
                $stdErr->writeln('');
                $stdErr->writeln($stdErr->isDecorated() ? '<bg=yellow;fg=black;>Detected deprecations in use:</>' : 'Detected deprecations in use:');
                foreach ($triggeredDeprecations as $deprecation) {
                    $stdErr->writeln(sprintf('- %s', $deprecation));
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongVersion()
    {
        $version = implode('', [
            parent::getLongVersion(),
            self::VERSION_CODENAME ? sprintf(' <info>%s</info>', self::VERSION_CODENAME) : '', // @phpstan-ignore-line to avoid `Ternary operator condition is always true|false.`
            ' by <comment>Fabien Potencier</comment> and <comment>Dariusz Ruminski</comment>',
        ]);

        $commit = '@git-commit@';

        if ('@'.'git-commit@' !== $commit) { // @phpstan-ignore-line as `$commit` is replaced during phar building
            $version .= ' ('.substr($commit, 0, 7).')';
        }

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return [new HelpCommand(), new ListCommand()];
    }
}
