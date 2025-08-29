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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpTag\\LinebreakAfterOpeningTagFixer <./../../../src/Fixer/PhpTag/LinebreakAfterOpeningTagFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpTag\\LinebreakAfterOpeningTagFixerTest <./../../../tests/Fixer/PhpTag/LinebreakAfterOpeningTagFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
