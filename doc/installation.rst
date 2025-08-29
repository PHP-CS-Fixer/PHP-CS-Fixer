============
Installation
============

When developing on multiple projects or with team of developers, it is highly recommended to install PHP CS Fixer as direct dependency per project and not globally on your machine.
This will ensure each team member is using version of the tool expected by given project.

PHP needs to be a minimum version of PHP 7.4.

Composer
--------

Fresh installation
~~~~~~~~~~~~~~~~~~

To install PHP CS Fixer, `install Composer <https://getcomposer.org/download/>`_ and issue the following command:

.. code-block:: console

    composer require --dev friendsofphp/php-cs-fixer
    ## or when facing conflicts in dependencies:
    composer require --dev php-cs-fixer/shim

Upgrade
~~~~~~~

.. code-block:: console

    composer upgrade friendsofphp/php-cs-fixer
    ## or
    composer upgrade php-cs-fixer/shim

Docker
------

You can use pre-built Docker images to run ``php-cs-fixer``.

.. code-block:: console

    docker run -it --rm -v $(pwd):/code ghcr.io/php-cs-fixer/php-cs-fixer:${FIXER_VERSION:-3-php8.3} fix src

``$FIXER_VERSION`` used in example above is an identifier of a release you want to use, which is based on Fixer and PHP versions combined. There are different tags for each Fixer's SemVer level and PHP version with syntax ``<php-cs-fixer-version>-php<php-version>``. For example:

* ``3.66.1-php7.4``
* ``3.66-php8.0``
* ``3-php8.3``

PHIVE
-----

Fresh installation
~~~~~~~~~~~~~~~~~~

Install `PHIVE <https://phar.io>`_ and issue the following command:

.. code-block:: console

    phive install php-cs-fixer

Upgrade
~~~~~~~

.. code-block:: console

    phive update php-cs-fixer

GitHub Action (Docker)
----------------------

To integrate PHP CS Fixer as check into a GitHub Action step, you can use a configuration like this:

.. code-block:: yaml

    - name: PHP-CS-Fixer
      uses: docker://ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3
        with:
          args: check
          # use `check .` if your repository not having paths configured in .php-cs-fixer[.dist].php

Gitlab-CI (Docker)
------------------

To integrate PHP CS Fixer as check into Gitlab-CI, you can use a configuration like this:

.. code-block:: yaml

    php-cs-fixer:
      image: ghcr.io/php-cs-fixer/php-cs-fixer:${FIXER_VERSION:-3-php8.3}
      script:
        php-cs-fixer check # --format gitlab ## specify format if not using PHP_CS_FIXER_FUTURE_MODE or v4+
        # use `check .` if your repository not having paths configured in .php-cs-fixer[.dist].php

Homebrew (globally)
-------------------

While not recommended to install the tool globally, it is possible to use homebrew as well.

Fresh installation
~~~~~~~~~~~~~~~~~~

.. code-block:: console

    brew install php-cs-fixer

Upgrade
~~~~~~~

.. code-block:: console

    brew upgrade php-cs-fixer

Manual binary download
----------------------

It is also possible to download the `php-cs-fixer.phar`_ file and store it somewhere on your computer.

Fresh installation
~~~~~~~~~~~~~~~~~~

To do that, you can run these commands to easily access latest ``php-cs-fixer`` from anywhere on
your system:

.. code-block:: console

    wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O php-cs-fixer
    # or
    curl -L https://cs.symfony.com/download/php-cs-fixer-v3.phar -o php-cs-fixer

or with specified version:

.. code-block:: console

    wget https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v3.66.0/php-cs-fixer.phar -O php-cs-fixer
    # or
    curl -L https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v3.66.0/php-cs-fixer.phar -o php-cs-fixer

then:

.. code-block:: console

    sudo chmod a+x php-cs-fixer
    sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Upgrade
~~~~~~~

.. code-block:: console

    sudo php-cs-fixer self-update

.. _php-cs-fixer.phar: https://cs.symfony.com/download/php-cs-fixer-v3.phar

