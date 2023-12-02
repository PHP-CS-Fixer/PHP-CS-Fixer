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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTrimFixer <./../../../src/Fixer/Phpdoc/PhpdocTrimFixer.php>`_
