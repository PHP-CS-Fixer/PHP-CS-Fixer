=====================================
Rule ``no_useless_try_catch_finally``
=====================================

Exceptions should not be caught to only be thrown. A ``finally`` statement must
not be empty.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -try {
   + 
        foo();
   -} catch(\Exception $e) {
   -    throw $e;
   -}
   +   
   +     
   +

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    try {
        foo();
    } catch(\Exception $e) {
        echo 1;
    }
   -finally {}
   + 

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_useless_try_catch_finally`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_useless_try_catch_finally`` rule.
