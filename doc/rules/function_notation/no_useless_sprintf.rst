===========================
Rule ``no_useless_sprintf``
===========================

There must be no ``sprintf`` calls with only the first argument.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when if the ``sprintf`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = sprintf('bar');
   +$foo = 'bar';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\NoUselessSprintfFixer <./../../../src/Fixer/FunctionNotation/NoUselessSprintfFixer.php>`_
