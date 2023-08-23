==========================
Rule ``short_scalar_cast``
==========================

Cast ``(boolean)`` and ``(integer)`` should be written as ``(bool)`` and
``(int)``, ``(double)`` and ``(real)`` as ``(float)``, ``(binary)`` as
``(string)``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = (boolean) $b;
   -$a = (integer) $b;
   -$a = (double) $b;
   +$a = (bool) $b;
   +$a = (int) $b;
   +$a = (float) $b;

   -$a = (binary) $b;
   +$a = (string) $b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

