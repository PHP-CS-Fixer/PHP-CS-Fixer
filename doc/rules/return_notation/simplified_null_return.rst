===============================
Rule ``simplified_null_return``
===============================

A return statement wishing to return ``void`` should not return ``null``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php return null;
   +<?php return;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo() { return null; }
   +function foo() { return; }
    function bar(): int { return null; }
    function baz(): ?int { return null; }
   -function xyz(): void { return null; }
   +function xyz(): void { return; }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ReturnNotation\\SimplifiedNullReturnFixer <./../../../src/Fixer/ReturnNotation/SimplifiedNullReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ReturnNotation\\SimplifiedNullReturnFixerTest <./../../../tests/Fixer/ReturnNotation/SimplifiedNullReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
