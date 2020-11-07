=====================================
Rule ``no_spaces_inside_parenthesis``
=====================================

There MUST NOT be a space after the opening parenthesis. There MUST NOT be a
space before the closing parenthesis.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -if ( $a ) {
   -    foo( );
   +if ($a) {
   +    foo();
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -function foo( $bar, $baz )
   +function foo($bar, $baz)
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``no_spaces_inside_parenthesis`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_spaces_inside_parenthesis`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_spaces_inside_parenthesis`` rule.
