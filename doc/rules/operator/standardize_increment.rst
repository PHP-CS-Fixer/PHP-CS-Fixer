==============================
Rule ``standardize_increment``
==============================

Increment and decrement operators should be used if possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$i += 1;
   +++$i;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$i -= 1;
   +--$i;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``standardize_increment`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``standardize_increment`` rule.
