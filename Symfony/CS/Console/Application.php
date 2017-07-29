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

namespace Symfony\CS\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\CS\Console\Command\FixCommand;
use Symfony\CS\Console\Command\ReadmeCommand;
use Symfony\CS\Console\Command\SelfUpdateCommand;
use Symfony\CS\Fixer;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('PHP CS Fixer', Fixer::VERSION);

        $this->add(new FixCommand());
        $this->add(new ReadmeCommand());
        $this->add(new SelfUpdateCommand());
    }

    public function getLongVersion()
    {
        $version = parent::getLongVersion().' by <comment>Fabien Potencier</comment> and <comment>Dariusz Ruminski</comment>';
        $commit = '@git-commit@';

        if ('@'.'git-commit@' !== $commit) {
            $version .= ' ('.substr($commit, 0, 7).')';
        }

        return $version;
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
            $warningsDetector = new WarningsDetector();
            $warningsDetector->detectOldVendor();
            $warningsDetector->detectOldMajor();

            if (FixCommand::COMMAND_NAME === $this->getCommandName($input)) {
                $warningsDetector->detectXdebug();
            }

            foreach ($warningsDetector->getWarnings() as $warning) {
                $stdErr->writeln(sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', $warning));
            }
        }

        return parent::doRun($input, $output);
    }
}
