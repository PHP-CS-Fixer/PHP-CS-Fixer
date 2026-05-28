========================
Rule ``psr_autoloading``
========================

Classes must be in a path that matches their namespace, be at least one
namespace deep and the class name should match the file name.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

This fixer may change your class name, which will break the code that depends on
the old name.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``dir``.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\PsrAutoloadingFixer <./../../../src/Fixer/Basic/PsrAutoloadingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\PsrAutoloadingFixerTest <./../../../tests/Fixer/Basic/PsrAutoloadingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
