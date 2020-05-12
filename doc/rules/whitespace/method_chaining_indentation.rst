====================================
Rule ``method_chaining_indentation``
====================================

Method chaining MUST be properly indented. Method chaining with different levels
of indentation is not supported.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $user->setEmail('voff.web@gmail.com')
   -         ->setPassword('233434');
   +    ->setPassword('233434');

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``method_chaining_indentation`` rule.
