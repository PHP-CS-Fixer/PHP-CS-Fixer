=============
Rule ``psr4``
=============

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``psr_autoloading`` instead.

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
    <?php
    namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +class PsrAutoloadingFixer {}
