==========================================
Rule ``not_operator_with_successor_space``
==========================================

Logical NOT operators (``!``) should have one trailing whitespace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if (!$bar) {
   +if (! $bar) {
        echo "Help!";
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NotOperatorWithSuccessorSpaceFixer <./../../../src/Fixer/Operator/NotOperatorWithSuccessorSpaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NotOperatorWithSuccessorSpaceFixerTest <./../../../tests/Fixer/Operator/NotOperatorWithSuccessorSpaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
