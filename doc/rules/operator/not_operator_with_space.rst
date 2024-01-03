================================
Rule ``not_operator_with_space``
================================

Logical NOT operators (``!``) should have leading and trailing whitespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if (!$bar) {
   +if ( ! $bar) {
        echo "Help!";
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NotOperatorWithSpaceFixer <./../../../src/Fixer/Operator/NotOperatorWithSpaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NotOperatorWithSpaceFixerTest <./../../../tests/Fixer/Operator/NotOperatorWithSpaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
