==============================
Rule ``no_superfluous_elseif``
==============================

Replaces superfluous ``elseif`` with ``if``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($a) {
        return 1;
   -} elseif ($b) {
   +}
   +if ($b) {
        return 2;
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoSuperfluousElseifFixer <./../../../src/Fixer/ControlStructure/NoSuperfluousElseifFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoSuperfluousElseifFixerTest <./../../../tests/Fixer/ControlStructure/NoSuperfluousElseifFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
