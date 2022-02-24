==============================
Rule ``no_duplicated_imports``
==============================

The should be duplicate ``use`` imports.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use Throwable;
   -use Throwable; // duplicate
   +  // duplicate

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_duplicated_imports`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_duplicated_imports`` rule.
