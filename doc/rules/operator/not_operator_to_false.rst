==============================
Rule ``not_operator_to_false``
==============================

Not operator should be replaced by false ==

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky with methods returning bool|null (i.e. strpos, preg_match...). Risky when
testing non null values

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if (!$bar) {
   +if (false === $bar) {
        echo "Help!";
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NotOperatorToFalseFixer <./../../../src/Fixer/Operator/NotOperatorToFalseFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NotOperatorToFalseFixerTest <./../../../tests/Fixer/Operator/NotOperatorToFalseFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
