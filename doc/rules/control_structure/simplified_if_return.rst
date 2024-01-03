=============================
Rule ``simplified_if_return``
=============================

Simplify ``if`` control structures that return the boolean result of their
condition.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if ($foo) { return true; } return false;
   +return (bool) ($foo)      ;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\SimplifiedIfReturnFixer <./../../../src/Fixer/ControlStructure/SimplifiedIfReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\SimplifiedIfReturnFixerTest <./../../../tests/Fixer/ControlStructure/SimplifiedIfReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
