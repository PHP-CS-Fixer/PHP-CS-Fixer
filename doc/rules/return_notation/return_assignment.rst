==========================
Rule ``return_assignment``
==========================

Local, dynamic and directly referenced variables should not be assigned and
directly returned by a function or method.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function a() {
   -    $a = 1;
   -    return $a;
   +    return 1;
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ReturnNotation\\ReturnAssignmentFixer <./../../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ReturnNotation\\ReturnAssignmentFixerTest <./../../../tests/Fixer/ReturnNotation/ReturnAssignmentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
