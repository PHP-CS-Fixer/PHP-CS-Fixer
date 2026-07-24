==================================
Rule ``phpdoc_no_duplicate_types``
==================================

Removes duplicate PHPDoc types.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param string|string|int $bar
   + * @param string|int $bar
     */

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoDuplicateTypesFixer <./../../../src/Fixer/Phpdoc/PhpdocNoDuplicateTypesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoDuplicateTypesFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocNoDuplicateTypesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
