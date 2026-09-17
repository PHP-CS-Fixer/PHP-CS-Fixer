===============================
Rule ``phpdoc_no_empty_return``
===============================

``@return void`` and ``@return null`` annotations must be removed from PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @return null
    */
    function foo() {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @return void
    */
    function foo() {}

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoEmptyReturnFixer <./../../../src/Fixer/Phpdoc/PhpdocNoEmptyReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoEmptyReturnFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocNoEmptyReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
