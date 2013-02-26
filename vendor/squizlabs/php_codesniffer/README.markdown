About
-----

PHP\_CodeSniffer is a PHP5 script that tokenises PHP, JavaScript and CSS files to detect violations of a defined coding standard. It is an essential development tool that ensures your code remains clean and consistent. It can also help prevent some common semantic errors made by developers.

[![Build Status](https://secure.travis-ci.org/squizlabs/PHP_CodeSniffer.png?branch=master)](https://travis-ci.org/squizlabs/PHP_CodeSniffer)

Requirements
------------

PHP\_CodeSniffer requires PHP version 5.1.2 or greater, although individual sniffs may have additional requirements such as external applications and scripts. See the [Configuration Options manual page](http://pear.php.net/manual/en/package.php.php-codesniffer.config-options.php) for a list of these requirements.

The SVN pre-commit hook requires PHP version 5.2.4 or greater due to its use of the vertical whitespace character.

Installation
------------

The easiest way to install PHP\_CodeSniffer is to use the PEAR installer. This will make the `phpcs` command immediately available for use. To install PHP\_CodeSniffer using the PEAR installer, first ensure you have [installed PEAR](http://pear.php.net/manual/en/installation.getting.php) and then run the following command:

    pear install PHP_CodeSniffer

If you use [Composer](http://getcomposer.org/), include a dependency for `squizlabs/php_codesniffer` in your `composer.json` file. For example:

    {
        "require": {
            "squizlabs/php_codesniffer": "1.*"
        }
    }

You will then be able to run PHP_CodeSniffer from the vendor bin directory:

    ./vendor/bin/phpcs -h

You can also download the PHP\_CodeSniffer source and run the `phpcs` command directly from the GIT checkout:

    git clone git://github.com/squizlabs/PHP_CodeSniffer.git
    cd PHP_CodeSniffer
    php scripts/phpcs -h

Documentation
-------------

The documentation for PHP\_CodeSniffer is available in the [PEAR manual](http://pear.php.net/manual/en/package.php.php-codesniffer.php).

Information about upcoming features and releases is available on the [Squiz Labs blog](http://www.squizlabs.com/php-codesniffer).

Contributing
-------------

If you do contribute code to PHP\_CodeSniffer, please make sure it conforms to the PEAR coding standard and that the PHP\_CodeSniffer unit tests still pass. The easiest way to contribute is to work on a checkout of the repository, or your own fork, rather than an installed PEAR version. If you do this, you can run the following commands to check if everything is ready to submit:

    cd PHP_CodeSniffer
    php scripts/phpcs --ignore=*/tests/* . -n

Which should give you no output, indicating that there are no PEAR coding standard errors. And then:

    phpunit tests/AllTests.php

Which should give you no failures or errors. You can ignore any skipped tests as these are for external tools.

Issues
------

Bug reports and feature requests can be submitted on the [PEAR bug tracker](http://pear.php.net/package/PHP_CodeSniffer/bugs).
