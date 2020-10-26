=======================
Rule ``lowercase_cast``
=======================

Cast should be written in lower case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,14 +1,14 @@
    <?php
   -    $a = (BOOLEAN) $b;
   -    $a = (BOOL) $b;
   -    $a = (INTEGER) $b;
   -    $a = (INT) $b;
   -    $a = (DOUBLE) $b;
   -    $a = (FLoaT) $b;
   -    $a = (reaL) $b;
   -    $a = (flOAT) $b;
   -    $a = (sTRING) $b;
   -    $a = (ARRAy) $b;
   -    $a = (OBJect) $b;
   -    $a = (UNset) $b;
   -    $a = (Binary) $b;
   +    $a = (boolean) $b;
   +    $a = (bool) $b;
   +    $a = (integer) $b;
   +    $a = (int) $b;
   +    $a = (double) $b;
   +    $a = (float) $b;
   +    $a = (real) $b;
   +    $a = (float) $b;
   +    $a = (string) $b;
   +    $a = (array) $b;
   +    $a = (object) $b;
   +    $a = (unset) $b;
   +    $a = (binary) $b;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,13 +1,13 @@
    <?php
   -    $a = (BOOLEAN) $b;
   -    $a = (BOOL) $b;
   -    $a = (INTEGER) $b;
   -    $a = (INT) $b;
   -    $a = (DOUBLE) $b;
   -    $a = (FLoaT) $b;
   -    $a = (flOAT) $b;
   -    $a = (sTRING) $b;
   -    $a = (ARRAy) $b;
   -    $a = (OBJect) $b;
   -    $a = (UNset) $b;
   -    $a = (Binary) $b;
   +    $a = (boolean) $b;
   +    $a = (bool) $b;
   +    $a = (integer) $b;
   +    $a = (int) $b;
   +    $a = (double) $b;
   +    $a = (float) $b;
   +    $a = (float) $b;
   +    $a = (string) $b;
   +    $a = (array) $b;
   +    $a = (object) $b;
   +    $a = (unset) $b;
   +    $a = (binary) $b;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``lowercase_cast`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``lowercase_cast`` rule.
