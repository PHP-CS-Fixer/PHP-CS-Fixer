=========================
Rule ``fopen_flag_order``
=========================

Order the flags in ``fopen`` calls, ``b`` and ``t`` must be last.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``fopen`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = fopen($foo, 'br+');
   +$a = fopen($foo, 'r+b');

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\FopenFlagOrderFixer <./../../../src/Fixer/FunctionNotation/FopenFlagOrderFixer.php>`_
