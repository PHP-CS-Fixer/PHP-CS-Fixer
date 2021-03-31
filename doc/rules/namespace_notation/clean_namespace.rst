========================
Rule ``clean_namespace``
========================

Namespace must not contain spacing, comments or PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -namespace Foo \ Bar;
   +namespace Foo\Bar;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo foo /* comment */ \ bar();
   +echo foo\bar();

Rule sets
---------

The rule is part of the following rule sets:

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``clean_namespace`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``clean_namespace`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``clean_namespace`` rule.
