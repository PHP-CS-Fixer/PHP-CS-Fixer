===========================
Rule ``no_short_bool_cast``
===========================

Short cast ``bool`` using double exclamation mark should not be used.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = !!$b;
   +$a = (bool)$b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\CastNotation\\NoShortBoolCastFixer <./../../../src/Fixer/CastNotation/NoShortBoolCastFixer.php>`_
