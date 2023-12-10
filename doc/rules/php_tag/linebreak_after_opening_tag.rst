====================================
Rule ``linebreak_after_opening_tag``
====================================

Ensure there is no code on the same line as the PHP open tag.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 1;
   +<?php
   +$a = 1;
    $b = 3;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\PhpTag\\LinebreakAfterOpeningTagFixer <./../../../src/Fixer/PhpTag/LinebreakAfterOpeningTagFixer.php>`_
