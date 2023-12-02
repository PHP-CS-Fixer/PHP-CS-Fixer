=====================================
Rule ``no_spaces_inside_parenthesis``
=====================================

There MUST NOT be a space after the opening parenthesis. There MUST NOT be a
space before the closing parenthesis.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``spaces_inside_parentheses`` instead.

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
Source class
------------

`PhpCsFixer\\Fixer\\Whitespace\\NoSpacesInsideParenthesisFixer <./../../../src/Fixer/Whitespace/NoSpacesInsideParenthesisFixer.php>`_
