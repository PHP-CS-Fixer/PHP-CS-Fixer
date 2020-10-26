==============================
Rule ``no_superfluous_elseif``
==============================

Replaces superfluous ``elseif`` with ``if``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,7 @@
    <?php
    if ($a) {
        return 1;
   -} elseif ($b) {
   +}
   +if ($b) {
        return 2;
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_superfluous_elseif`` rule.
