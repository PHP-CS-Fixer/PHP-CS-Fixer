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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\NoBlankLinesAfterPhpdocFixer <./../../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\NoBlankLinesAfterPhpdocFixerTest <./../../../tests/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
