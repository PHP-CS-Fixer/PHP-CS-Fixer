=========================
Rule ``full_opening_tag``
=========================

PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not
other tag variations.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?
   +<?php

    echo "Hello!";

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR1 <./../../ruleSets/PSR1.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpTag\\FullOpeningTagFixer <./../../../src/Fixer/PhpTag/FullOpeningTagFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpTag\\FullOpeningTagFixerTest <./../../../tests/Fixer/PhpTag/FullOpeningTagFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
