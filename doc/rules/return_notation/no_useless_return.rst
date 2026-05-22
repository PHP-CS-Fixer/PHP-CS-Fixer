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

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ReturnNotation\\NoUselessReturnFixer <./../../../src/Fixer/ReturnNotation/NoUselessReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ReturnNotation\\NoUselessReturnFixerTest <./../../../tests/Fixer/ReturnNotation/NoUselessReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
