=======================================
Rule ``switch_case_semicolon_to_colon``
=======================================

A case should be followed by a colon and not a semicolon.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
        switch ($a) {
   -        case 1;
   +        case 1:
                break;
   -        default;
   +        default:
                break;
        }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PHP8.5Migration <./../../ruleSets/PHP8.5Migration.rst>`_
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\SwitchCaseSemicolonToColonFixer <./../../../src/Fixer/ControlStructure/SwitchCaseSemicolonToColonFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\SwitchCaseSemicolonToColonFixerTest <./../../../tests/Fixer/ControlStructure/SwitchCaseSemicolonToColonFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
