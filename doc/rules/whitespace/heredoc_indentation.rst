============================
Rule ``heredoc_indentation``
============================

Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
        $a = <<<EOD
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
        $a = <<<'EOD'
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

Rule sets
---------

The rule is part of the following rule sets:

@PHP73Migration
  Using the ``@PHP73Migration`` rule set will enable the ``heredoc_indentation`` rule.

@PHP80Migration
  Using the ``@PHP80Migration`` rule set will enable the ``heredoc_indentation`` rule.
