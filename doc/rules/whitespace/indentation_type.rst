=========================
Rule ``indentation_type``
=========================

Code MUST use configured indentation type.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php

    if (true) {
   -	echo 'Hello!';
   +    echo 'Hello!';
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``indentation_type`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``indentation_type`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``indentation_type`` rule.
