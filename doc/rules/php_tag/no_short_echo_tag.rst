==========================
Rule ``no_short_echo_tag``
==========================

Replace short-echo ``<?=`` with long format ``<?php echo`` syntax.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?= "foo";
   +<?php echo "foo";

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_short_echo_tag`` rule.
