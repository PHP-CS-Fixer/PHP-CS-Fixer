=========================
Rule ``no_empty_comment``
=========================

There should not be any empty comments.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -//
   -#
   -/* */
   +
   +
   +

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_empty_comment`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_empty_comment`` rule.
