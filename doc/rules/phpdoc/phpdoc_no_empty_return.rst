===============================
Rule ``phpdoc_no_empty_return``
===============================

``@return void`` and ``@return null`` annotations should be omitted from PHPDoc.

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

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoEmptyReturnFixer <./../../../src/Fixer/Phpdoc/PhpdocNoEmptyReturnFixer.php>`_
