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
Source class
------------

`PhpCsFixer\\Fixer\\ReturnNotation\\SimplifiedNullReturnFixer <./../../../src/Fixer/ReturnNotation/SimplifiedNullReturnFixer.php>`_
