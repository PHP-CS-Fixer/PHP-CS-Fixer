===================================
Rule ``phpdoc_types_no_duplicates``
===================================

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesNoDuplicatesFixer <./../../../src/Fixer/Phpdoc/PhpdocTypesNoDuplicatesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocTypesNoDuplicatesFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocTypesNoDuplicatesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
