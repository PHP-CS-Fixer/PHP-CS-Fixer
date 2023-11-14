========================
Rule ``psr_autoloading``
========================

Classes must be in a path that matches their namespace, be at least one
namespace deep and the class name should match the file name.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

This fixer may change your class name, which will break the code that depends on
the old name.

Configuration
-------------

``dir``
~~~~~~~

If provided, the directory where the project code is placed.

Allowed types: ``null`` and ``string``

Default value: ``null``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +class PsrAutoloadingFixer {}

Example #2
~~~~~~~~~~

With configuration: ``['dir' => './src']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +namespace PhpCsFixer\Fixer\Basic;
   +class PsrAutoloadingFixer {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Basic\\PsrAutoloadingFixer <./../src/Fixer/Basic/PsrAutoloadingFixer.php>`_
