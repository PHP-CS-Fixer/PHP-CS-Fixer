<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

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
                new InputArgument('path', InputArgument::REQUIRED, 'The path'),
                new InputArgument('finder', InputArgument::OPTIONAL, 'The Finder short class name to use', 'SymfonyFinder'),
                new InputOption('dry-run', '', InputOption::VALUE_NONE, 'Only shows which files would have been modified'),
                new InputOption('level', '', InputOption::VALUE_REQUIRED, 'The level of fixes (can be psr1, psr2, or all)', 'all'),
            ))
            ->setDescription('Fixes a directory or a file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command tries to fix as much coding standards
problems as possible:

    <info>php %command.full_name% /path/to/dir</info>
    or
    <info>php %command.full_name% /path/to/file</info>

You can limit the fixers you want to use on your project by using the
<comment>--level<comment> option:

    <info>php %command.full_name% /path/to/project --level=psr1</info>
    <info>php %command.full_name% /path/to/project --level=psr2</info>
    <info>php %command.full_name% /path/to/project --level=all</info>

When the level option is not passed, all PSR2 fixers and some additional ones
are run.

You can tweak the files and directories being analyzed by creating a
<comment>.php_cs</comment> file in the root directory of your project:

    <?php

    return Symfony\Component\Finder\Finder::create()
        ->name('*.php')
        ->exclude('someDir')
        ->in(__DIR__)
    ;

The <comment>.php_cs</comment> file must return a PHP iterator, like a Symfony
Finder instance.

You can also use specialized "finders", for instance when ran for Symfony
2.0 or 2.1:

        <info>php %command.full_name% /path/to/sf20 Symfony21Finder</info>
        <info>php %command.full_name% /path/to/sf21 Symfony21Finder</info>

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

        $path = $input->getArgument('path');
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($path)) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
        }

        if (is_file($path)) {
            $iterator = new \ArrayIterator(array(new \SplFileInfo($path)));
        } elseif (file_exists($config = $path.'/.php_cs')) {
            $iterator = include $config;
        } else {
            $class = 'Symfony\\CS\\Finder\\'.$input->getArgument('finder');
            $iterator = new $class($path);
        }

        switch ($input->getOption('level')) {
            case 'psr1':
                $level = FixerInterface::PSR1_LEVEL;
                break;
            case 'psr2':
                $level = FixerInterface::PSR2_LEVEL;
                break;
            case 'all':
                $level = FixerInterface::ALL_LEVEL;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The level "%s" is not defined.', $input->getOption('level')));
        }

        $changed = $fixer->fix($iterator, $level, $input->getOption('dry-run'));

        foreach ($changed as $i => $file) {
            $output->writeln(sprintf('%4d) %s', $i, $file));
        }
    }
}
