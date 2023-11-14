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

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\DeclareParenthesesFixer <./../src/Fixer/LanguageConstruct/DeclareParenthesesFixer.php>`_
