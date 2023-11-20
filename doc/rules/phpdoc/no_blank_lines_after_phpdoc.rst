====================================
Rule ``no_blank_lines_after_phpdoc``
====================================

There should not be blank lines between docblock and the documented element.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /**
     * This is the bar class.
     */
   -
   -
    class Bar {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\NoBlankLinesAfterPhpdocFixer <./../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php>`_
