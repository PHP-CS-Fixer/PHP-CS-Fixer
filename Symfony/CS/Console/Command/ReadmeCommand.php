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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ReadmeCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('readme')
            ->setDescription('Generates the README content, based on the fix command help')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header = <<<EOF
PHP Coding Standard Fixer
=========================

`PHP_CodeSniffer` is a good tool to find coding standards problems in your
project but the identified problems need to be fixed by hand, and frankly,
this is quite boring on large projects! The goal of the PHP coding Standard
Fixer tool is to automate the fixing of *most* issues.

The tool knows how to fix issues for the coding standards defined in the
soon-to-be-available PSR-1 and PSR-2 documents.

Installation
------------

Download the
[`php-cs-fixer.phar`](https://github.com/fabpot/PHP-CS-Fixer/raw/master/php-cs-fixer.phar)
file and store it somewhere on your computer.

Usage
-----

EOF;

        $footer = <<<EOF

Helpers
-------

If you are using Vim, install the dedicated
[plugin](https://github.com/stephpy/vim-php-cs-fixer).

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to contribute more of them.

### Fixers

A *fixer* is a class that tries to fix one CS issue (a `Fixer` class must
implement `FixerInterface`).

### Configs

A *config* knows about the CS level and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful
for projects that follow a well-known directory structures (like for Symfony
projects for instance).

EOF;

        $command = $this->getApplication()->get('fix');
        $help = $command->getHelp();
        $help = str_replace('%command.full_name%', 'php-cs-fixer.phar '.$command->getName(), $help);
        $help = str_replace('%command.name%', $command->getName(), $help);
        $help = preg_replace('#</?(comment|info)>#', '`', $help);
        $help = preg_replace('#^(\s+)`(.+)`$#m', '$1$2', $help);
        $help = preg_replace('#^ \* `(.+)`#m', ' * $1', $help);

        $output->writeln($header);
        $output->writeln($help);
        $output->write($footer);
    }
}
