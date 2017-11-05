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
   @@ -1,8 +1,5 @@
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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_trim`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_trim`` rule.
