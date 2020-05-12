====================
Rule ``phpdoc_trim``
====================

PHPDoc should start and end with content, excluding the very first and last line
of the docblocks.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - *
     * Foo must be final class.
   - *
   - *
     */
    final class Foo {}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_trim`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_trim`` rule.
