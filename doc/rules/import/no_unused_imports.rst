==========================
Rule ``no_unused_imports``
==========================

Unused ``use`` statements must be removed.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use \DateTime;
   -use \Exception;

    new DateTime();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\NoUnusedImportsFixer <./../../../src/Fixer/Import/NoUnusedImportsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\NoUnusedImportsFixerTest <./../../../tests/Fixer/Import/NoUnusedImportsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
