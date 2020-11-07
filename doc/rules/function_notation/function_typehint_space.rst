================================
Rule ``function_typehint_space``
================================

Ensure single space between function's argument and its typehint.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -function sample(array$a)
   +function sample(array $a)
    {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -function sample(array  $a)
   +function sample(array $a)
    {}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``function_typehint_space`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``function_typehint_space`` rule.
