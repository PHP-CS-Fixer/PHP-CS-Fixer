=========================================
Rule ``no_alias_language_construct_call``
=========================================

Master language constructs shall be used instead of aliases.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -die;
   +exit;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\NoAliasLanguageConstructCallFixer <./../../../src/Fixer/Alias/NoAliasLanguageConstructCallFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\NoAliasLanguageConstructCallFixerTest <./../../../tests/Fixer/Alias/NoAliasLanguageConstructCallFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
