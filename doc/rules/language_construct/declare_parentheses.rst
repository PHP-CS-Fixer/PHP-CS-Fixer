============================
Rule ``declare_parentheses``
============================

There must not be spaces around ``declare`` statement parentheses.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php declare ( strict_types=1 );
   +<?php declare(strict_types=1);

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\DeclareParenthesesFixer <./../../../src/Fixer/LanguageConstruct/DeclareParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\DeclareParenthesesFixerTest <./../../../tests/Fixer/LanguageConstruct/DeclareParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
