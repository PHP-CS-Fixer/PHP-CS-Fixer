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
PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer tool fixes *most* issues in your code when you
want to follow the PHP coding standards as defined in the PSR-1 and PSR-2
documents.

If you are already using `PHP_CodeSniffer` to identify coding standards
problems in your code, you know that fixing them by hand is tedious,
especially on large projects. This tool does the job for you.

Installation
------------

### Locally

Download the
[`php-cs-fixer.phar`](http://cs.sensiolabs.org/get/php-cs-fixer.phar) file and
store it somewhere on your computer.

### Globally

You can run these commands to easily acces `php-cs-fixer` from anywhere on your system:

    \$ sudo wget http://cs.sensiolabs.org/get/php-cs-fixer.phar -O /usr/local/bin/php-cs-fixer

or with curl:

    \$ sudo curl http://cs.sensiolabs.org/get/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

then:

    \$ sudo chmod a+x /usr/local/bin/php-cs-fixer

Then, just run `php-cs-fixer` in order to run php-cs-fixer

Update
------

### Locally

The `self-update` command tries to update php-cs-fixer itself:

    \$ php php-cs-fixer.phar self-update

### Globally

You can update php-cs-fixer through this command:

    \$ sudo php-cs-fixer self-update

Usage
-----

EOF;

        $footer = <<<EOF

Helpers
-------

Dedicated plugins exist for:

* [Vim](https://github.com/stephpy/vim-php-cs-fixer)
* [Sublime Text 2](https://github.com/benmatselby/sublime-phpcs)

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to [contribute](https://github.com/fabpot/php-cs-fixer) more
of them.

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
        $help = preg_replace('#^ \* `(.+)`#m', ' * **$1**', $help);

        $output->writeln($header);
        $output->writeln($help);
        $output->write($footer);
    }
}
