========================
Rule ``no_empty_phpdoc``
========================

There should not be empty PHPDoc blocks.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php /**  */
   +<?php 

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\NoEmptyPhpdocFixer <./../../../src/Fixer/Phpdoc/NoEmptyPhpdocFixer.php>`_

Test class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\NoEmptyPhpdocFixer <./../../../tests/Fixer/Phpdoc/NoEmptyPhpdocFixerTest.php>`_
