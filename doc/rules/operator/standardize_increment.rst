==============================
Rule ``standardize_increment``
==============================

Increment and decrement operators should be used if possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$i += 1;
   +++$i;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$i -= 1;
   +--$i;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\StandardizeIncrementFixer <./../../../src/Fixer/Operator/StandardizeIncrementFixer.php>`_
