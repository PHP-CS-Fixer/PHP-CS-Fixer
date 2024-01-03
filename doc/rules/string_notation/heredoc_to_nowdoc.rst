==========================
Rule ``heredoc_to_nowdoc``
==========================

Convert ``heredoc`` to ``nowdoc`` where possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<"TEST"
   +<?php $a = <<<'TEST'
    Foo
    TEST;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\HeredocToNowdocFixer <./../../../src/Fixer/StringNotation/HeredocToNowdocFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\HeredocToNowdocFixerTest <./../../../tests/Fixer/StringNotation/HeredocToNowdocFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
