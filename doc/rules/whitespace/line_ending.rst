====================
Rule ``line_ending``
====================

All PHP files must use same line ending.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php $b = " $a ^M
   - 123"; $a = <<<TEST^M
   -AAAAA ^M
   - |^M
   + 123"; $a = <<<TEST
   +AAAAA 
   + |
    TEST;

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``line_ending`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``line_ending`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``line_ending`` rule.
