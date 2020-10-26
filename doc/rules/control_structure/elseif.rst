===============
Rule ``elseif``
===============

The keyword ``elseif`` should be used instead of ``else if`` so that all control
keywords look like single words.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
    if ($a) {
   -} else if ($b) {
   +} elseif ($b) {
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``elseif`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``elseif`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``elseif`` rule.
