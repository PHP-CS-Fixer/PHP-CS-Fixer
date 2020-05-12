===================================
Rule ``combine_consecutive_unsets``
===================================

Calling ``unset`` on multiple items should be done in one call.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -unset($a); unset($b);
   +unset($a, $b); 

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``combine_consecutive_unsets`` rule.
