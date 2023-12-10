======================
Rule ``constant_case``
======================

The PHP constants ``true``, ``false``, and ``null`` MUST be written using the
correct casing.

Configuration
-------------

``case``
~~~~~~~~

Whether to use the ``upper`` or ``lower`` case syntax.

Allowed values: ``'lower'`` and ``'upper'``

Default value: ``'lower'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = FALSE;
   -$b = True;
   -$c = nuLL;
   +$a = false;
   +$b = true;
   +$c = null;

Example #2
~~~~~~~~~~

With configuration: ``['case' => 'upper']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = FALSE;
   -$b = True;
   -$c = nuLL;
   +$b = TRUE;
   +$c = NULL;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Casing\\ConstantCaseFixer <./../../../src/Fixer/Casing/ConstantCaseFixer.php>`_
