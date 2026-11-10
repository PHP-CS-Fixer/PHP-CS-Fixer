==========================
Rule ``switch_case_space``
==========================

Removes extra spaces between colon and case value.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``colon_space`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
        switch($a) {
   -        case 1   :
   +        case 1:
                break;
   -        default     :
   +        default:
                return 2;
        }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\SwitchCaseSpaceFixer <./../../../src/Fixer/ControlStructure/SwitchCaseSpaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\SwitchCaseSpaceFixerTest <./../../../tests/Fixer/ControlStructure/SwitchCaseSpaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
