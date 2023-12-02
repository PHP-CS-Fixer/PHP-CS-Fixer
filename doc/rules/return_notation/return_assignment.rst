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

Source class
------------

`PhpCsFixer\\Fixer\\ReturnNotation\\ReturnAssignmentFixer <./../../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php>`_
