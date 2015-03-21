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
 *
 * @internal
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

If you are already using ``PHP_CodeSniffer`` to identify coding standards
problems in your code, you know that fixing them by hand is tedious, especially
on large projects. This tool does the job for you.

Requirements
------------

PHP needs to be a minimum version of PHP 5.3.6.

Installation
------------

Locally
~~~~~~~

Download the `php-cs-fixer.phar`_ file and store it somewhere on your computer.

Globally (manual)
~~~~~~~~~~~~~~~~~

You can run these commands to easily access ``php-cs-fixer`` from anywhere on
your system:

.. code-block:: bash

    \$ wget http://get.sensiolabs.org/php-cs-fixer.phar -O php-cs-fixer

or with curl:

.. code-block:: bash

    \$ curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer

then:

.. code-block:: bash

    \$ sudo chmod a+x php-cs-fixer
    \$ sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP-CS-Fixer, install Composer and issue the following command:

.. code-block:: bash

    $ ./composer.phar global require fabpot/php-cs-fixer

Then, make sure you have ``~/.composer/vendor/bin`` in your ``PATH``, and
you're good to go:

.. code-block:: bash

    export PATH="\$PATH:\$HOME/.composer/vendor/bin"

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

PHP-CS-Fixer is part of the homebrew-php project. Follow the installation
instructions at https://github.com/homebrew/homebrew-php if you don't
already have it.

.. code-block:: bash

    \$ brew install homebrew/php/php-cs-fixer

Update
------

Locally
~~~~~~~

The ``self-update`` command tries to update ``php-cs-fixer`` itself:

.. code-block:: bash

    \$ php php-cs-fixer.phar self-update

Globally (manual)
~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    \$ sudo php-cs-fixer self-update

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    \$ ./composer.phar global update fabpot/php-cs-fixer

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    \$ brew upgrade php-cs-fixer

Usage
-----

EOF;

        $footer = <<<EOF

Helpers
-------

Dedicated plugins exist for:

* `Vim`_
* `Sublime Text`_
* `NetBeans`_
* `PhpStorm`_

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to `contribute`_ more of them.

Fixers
~~~~~~

A *fixer* is a class that tries to fix one CS issue (a ``Fixer`` class must
implement ``FixerInterface``).

Configs
~~~~~~~

A *config* knows about the CS level and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful for
projects that follow a well-known directory structures (like for Symfony
projects for instance).

.. _php-cs-fixer.phar: http://get.sensiolabs.org/php-cs-fixer.phar
.. _Vim:               https://github.com/stephpy/vim-php-cs-fixer
.. _Sublime Text:      https://github.com/benmatselby/sublime-phpcs
.. _NetBeans:          http://plugins.netbeans.org/plugin/49042/php-cs-fixer
.. _PhpStorm:          http://arnolog.net/post/92715936483/use-fabpots-php-cs-fixer-tool-in-phpstorm-in-2-steps
.. _contribute:        https://github.com/FriendsOfPhp/php-cs-fixer/blob/master/CONTRIBUTING.md

EOF;

        $command = $this->getApplication()->get('fix');
        $help = $command->getHelp();
        $help = str_replace('%command.full_name%', 'php-cs-fixer.phar '.$command->getName(), $help);
        $help = str_replace('%command.name%', $command->getName(), $help);
        $help = preg_replace('#</?(comment|info)>#', '``', $help);
        $help = preg_replace('#^(\s+)``(.+)``$#m', '$1$2', $help);
        $help = preg_replace('#^ \* ``(.+)``#m', '* **$1**', $help);
        $help = preg_replace("#^\n( +)#m", "\n.. code-block:: bash\n\n$1", $help);
        $help = preg_replace("#^\.\. code-block:: bash\n\n( +<\?php)#m", ".. code-block:: php\n\n$1", $help);
        $help = preg_replace_callback(
            "#<\?php.*?\?>#s",
            function ($matches) {
                return preg_replace("#\n\n +\?>#", '', preg_replace("#^\.\. code-block:: bash\n\n#m", '', $matches[0]));
            },
            $help
        );
        $help = preg_replace('#^                        #m', '  ', $help);
        $help = preg_replace('#\*\* +\[#', '** [', $help);

        $output->write($header."\n".$help."\n".$footer);
    }
}
