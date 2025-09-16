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

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)*
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)*
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)*
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)*
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\CastNotation\\ShortScalarCastFixer <./../../../src/Fixer/CastNotation/ShortScalarCastFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\CastNotation\\ShortScalarCastFixerTest <./../../../tests/Fixer/CastNotation/ShortScalarCastFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
