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
Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\SimplifiedIfReturnFixer <./../../../src/Fixer/ControlStructure/SimplifiedIfReturnFixer.php>`_
