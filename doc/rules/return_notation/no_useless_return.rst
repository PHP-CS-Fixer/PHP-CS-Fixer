==========================
Rule ``no_useless_return``
==========================

There should not be an empty ``return`` statement at the end of a function.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function example($b) {
        if ($b) {
            return;
        }
   -    return;
   +    
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ReturnNotation\\NoUselessReturnFixer <./../../../src/Fixer/ReturnNotation/NoUselessReturnFixer.php>`_
