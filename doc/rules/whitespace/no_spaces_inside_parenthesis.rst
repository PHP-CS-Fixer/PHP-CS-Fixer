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
    <?php
   -function foo( $bar, $baz )
   +function foo($bar, $baz)
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

