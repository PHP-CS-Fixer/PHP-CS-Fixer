Symfony Coding Standard Fixer
=============================

This tool analyzes the Symfony source code to fix as much coding standards
problems as possible.

Download the `symfony-cs-fixer.phar` file and execute it:

    # For the Symfony 2.0 branch
    php symfony-cs-fixer.phar fix /path/to/symfony/src Symfony20Finder

    # For the Symfony 2.1/master branch
    php symfony-cs-fixer.phar fix /path/to/symfony/src Symfony21Finder

    # For a Symfony bundle or any other project using the same Symfony CS
    php symfony-cs-fixer.phar fix /path/to/bundle

See http://symfony.com/doc/current/contributing/code/standards.html for more
information about the Symfony Coding Standards.
