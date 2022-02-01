========================
Rule ``psr_autoloading``
========================

Classes must be in a path that matches their namespace, be at least one
namespace deep and the class name should match the file name.

.. warning:: Using this rule is risky.

   This fixer may change your class name, which will break the code that depends
   on the old name.

Configuration
-------------

``dir``
~~~~~~~

If provided, the directory where the project code is placed.

Allowed types: ``null``, ``string``

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

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``psr_autoloading`` rule with the default config.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``psr_autoloading`` rule with the default config.
