==========================
Rule ``no_useless_return``
==========================

There should not be an empty ``return`` statement at the end of a function.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function example($b) {
        if ($b) {
            return;
        }
   -    return;
   +    
    }

Rule sets
---------

The rule is part of the following rule sets:

@Laravel
  Using the `@Laravel <./../../ruleSets/Laravel.rst>`_ rule set will enable the ``no_useless_return`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_useless_return`` rule.
