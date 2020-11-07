=============
Rule ``psr4``
=============

Class names should match the file name.

.. warning:: Using this rule is risky.

   This fixer may change your class name, which will break the code that depends
   on the old name.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
    namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +class Psr4Fixer {}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``psr4`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``psr4`` rule.
