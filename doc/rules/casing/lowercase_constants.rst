============================
Rule ``lowercase_constants``
============================

The PHP constants ``true``, ``false``, and ``null`` MUST be in lower case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -$a = FALSE;
   -$b = True;
   -$c = nuLL;
   +$a = false;
   +$b = true;
   +$c = null;

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``lowercase_constants`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``lowercase_constants`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``lowercase_constants`` rule.
