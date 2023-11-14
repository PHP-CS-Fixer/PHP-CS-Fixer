================
Rule ``is_null``
================

Replaces ``is_null($var)`` expression with ``null === $var``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``is_null`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = is_null($b);
   +$a = null === $b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\IsNullFixer <./../src/Fixer/LanguageConstruct/IsNullFixer.php>`_
