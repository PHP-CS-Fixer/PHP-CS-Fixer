===============================
Rule ``standardize_not_equals``
===============================

Replace all ``<>`` with ``!=``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = $b <> $c;
   +$a = $b != $c;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``standardize_not_equals`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``standardize_not_equals`` rule.
