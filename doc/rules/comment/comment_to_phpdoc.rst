==========================
Rule ``comment_to_phpdoc``
==========================

Comments with annotation should be docblock when used on structural elements.

.. warning:: Using this rule is risky.

   Risky as new docblocks might mean more, e.g. a Doctrine entity might have a
   new column in database.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php /* header */ $x = true; /* @var bool $isFoo */ $isFoo = true;
   +<?php /* header */ $x = true; /** @var bool $isFoo */ $isFoo = true;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``comment_to_phpdoc`` rule.
