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
   -$a = (real) $b;
   +$a = (bool) $b;
   +$a = (int) $b;
   +$a = (float) $b;
   +$a = (float) $b;

   -$a = (binary) $b;
   +$a = (string) $b;

Example #2
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

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``short_scalar_cast`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``short_scalar_cast`` rule.
