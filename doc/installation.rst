============
Installation
============

Requirements
------------

PHP needs to be a minimum version of PHP 7.4.

Installation
------------

Locally
~~~~~~~

Download the `php-cs-fixer.phar`_ file and store it somewhere on your computer.

Globally (manual)
~~~~~~~~~~~~~~~~~

You can run these commands to easily access latest ``php-cs-fixer`` from anywhere on
your system:

.. code-block:: console

    wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O php-cs-fixer

or with specified version:

.. code-block:: console

    wget https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v3.13.0/php-cs-fixer.phar -O php-cs-fixer

or with curl:

.. code-block:: console

    curl -L https://cs.symfony.com/download/php-cs-fixer-v3.phar -o php-cs-fixer

then:

.. code-block:: console

    sudo chmod a+x php-cs-fixer
    sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP CS Fixer, `install Composer <https://getcomposer.org/download/>`_ and issue the following command:

.. code-block:: console

    composer global require friendsofphp/php-cs-fixer

Then make sure you have the global Composer binaries directory in your ``PATH``. This directory is platform-dependent, see `Composer documentation <https://getcomposer.org/doc/03-cli.md#composer-home>`_ for details. Example for some Unix systems:

.. code-block:: console

    export PATH="$PATH:$HOME/.composer/vendor/bin"

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

.. code-block:: console

    brew install php-cs-fixer

Locally (PHIVE)
~~~~~~~~~~~~~~~

Install `PHIVE <https://phar.io>`_ and issue the following command:

.. code-block:: console

    phive install php-cs-fixer # use `--global` for global install

Gitlab-CI (Docker)
~~~~~~~~~~~~~~~~~~

To integrate php-cs-fixer as check into Gitlab-CI, you can use a configuration like this:

.. code-block:: yaml

    php-cs-fixer:
      image: ghcr.io/php-cs-fixer/php-cs-fixer:${FIXER_VERSION:-3-php8.3}
      script:
        php-cs-fixer check --diff --format=txt src

Update
------

Locally
~~~~~~~

The ``self-update`` command tries to update ``php-cs-fixer`` itself:

.. code-block:: console

    php php-cs-fixer.phar self-update

Globally (manual)
~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: console

    sudo php-cs-fixer self-update

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: console

    ./composer.phar global update friendsofphp/php-cs-fixer

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: console

    brew upgrade php-cs-fixer

Locally (PHIVE)
~~~~~~~~~~~~~~~

.. code-block:: console

    phive update php-cs-fixer

.. _php-cs-fixer.phar: https://cs.symfony.com/download/php-cs-fixer-v3.phar
