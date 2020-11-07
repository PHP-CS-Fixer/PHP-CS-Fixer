===========================================
Rule ``no_blank_lines_after_class_opening``
===========================================

There should be no empty lines after class opening brace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,8 +1,7 @@
    <?php
    final class Sample
    {
   -
        protected function foo()
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_blank_lines_after_class_opening`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_blank_lines_after_class_opening`` rule.
