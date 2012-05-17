<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\Fixer;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FixCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDefinition(array(
                new InputArgument('dir', InputArgument::REQUIRED, 'The Symfony dir'),
                new InputArgument('finder', InputArgument::OPTIONAL, 'The Finder short class name to use', 'SymfonyFinder'),
            ))
            ->setDescription('Fixes a project')
            ->setHelp(<<<EOF
The <info>fix</info> command tries to fix as much coding standards
problems as possible:

    <info>php fixer /path/to/symfony/src Symfony21Finder</info>

See http://symfony.com/doc/current/contributing/code/standards.html for more
information about the Symfony Coding Standards.
EOF
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();

        $dir = $input->getArgument('dir');
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($dir)) {
            $dir = getcwd().DIRECTORY_SEPARATOR.$dir;
        }

        $class = 'Symfony\\CS\\Finder\\'.$input->getArgument('finder');
        $iterator = new $class($dir);

        $changed = $fixer->fix($iterator);

        foreach ($changed as $i => $file) {
            $output->writeln(sprintf('%4d) %s', $i, $file));
        }
    }
}
