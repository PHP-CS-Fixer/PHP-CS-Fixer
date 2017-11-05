=======================
Rule ``no_closing_tag``
=======================

The closing ``?>`` tag MUST be omitted from files containing only PHP.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,4 @@
    <?php
    class Sample
    {
   -}
   -?>
   +}
   \ No newline at end of file

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``no_closing_tag`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_closing_tag`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_closing_tag`` rule.
