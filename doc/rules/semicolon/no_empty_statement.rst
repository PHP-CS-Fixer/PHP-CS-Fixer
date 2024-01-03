===========================
Rule ``no_empty_statement``
===========================

Remove useless (semicolon) statements.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 1;;
   +<?php $a = 1;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo 1;2;
   +<?php echo 1;

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php while(foo()){
   -    continue 1;
   +    continue ;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Semicolon\\NoEmptyStatementFixer <./../../../src/Fixer/Semicolon/NoEmptyStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Semicolon\\NoEmptyStatementFixerTest <./../../../tests/Fixer/Semicolon/NoEmptyStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
