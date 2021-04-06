===================================
Rule ``combine_consecutive_issets``
===================================

Using ``isset($var) &&`` multiple times should be done in one call.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = isset($a) && isset($b);
   +$a = isset($a, $b)  ;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``combine_consecutive_issets`` rule.
