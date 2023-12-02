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
Source class
------------

`PhpCsFixer\\Fixer\\Operator\\NotOperatorWithSpaceFixer <./../../../src/Fixer/Operator/NotOperatorWithSpaceFixer.php>`_
