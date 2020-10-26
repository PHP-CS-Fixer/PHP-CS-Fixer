=========================
Rule ``no_binary_string``
=========================

There should not be a binary flag before strings.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $a = b'foo';
   +<?php $a = 'foo';

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
   -<?php $a = b<<<EOT
   +<?php $a = <<<EOT
    foo
    EOT;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_binary_string`` rule.
