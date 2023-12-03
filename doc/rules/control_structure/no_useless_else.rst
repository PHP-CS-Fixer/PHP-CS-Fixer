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

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\NoUselessElseFixer <./../../../src/Fixer/ControlStructure/NoUselessElseFixer.php>`_
