========================
Rule ``no_useless_else``
========================

There should not be useless ``else`` cases.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($a) {
        return 1;
   -} else {
   +}  
        return 2;
   -}
   +

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoUselessElseFixer <./../../../src/Fixer/ControlStructure/NoUselessElseFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoUselessElseFixerTest <./../../../tests/Fixer/ControlStructure/NoUselessElseFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
