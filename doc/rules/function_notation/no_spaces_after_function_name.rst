======================================
Rule ``no_spaces_after_function_name``
======================================

When making a method or function call, there MUST NOT be a space between the
method or function name and the opening parenthesis.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -require ('sample.php');
   -echo (test (3));
   -exit  (1);
   -$func ();
   +require('sample.php');
   +echo(test(3));
   +exit(1);
   +$func();

Rule sets
---------

The rule is part of the following rule sets:

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``no_spaces_after_function_name`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``no_spaces_after_function_name`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_spaces_after_function_name`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_spaces_after_function_name`` rule.
