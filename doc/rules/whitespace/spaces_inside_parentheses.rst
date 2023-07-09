==================================
Rule ``spaces_inside_parentheses``
==================================

Parenthesis must be declared using the configured syntax.

Configuration
-------------

``space``
~~~~~~~~~

Whether to have ``spaces`` or ``none`` spaces inside parenthesis.

Allowed values: ``'none'`` and ``'spaces'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

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

With configuration: ``['space' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo( $bar, $baz )
   +function foo($bar, $baz)
    {
    }

Example #3
~~~~~~~~~~

With configuration: ``['space' => 'spaces']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if ($a) {
   -    foo( );
   +if ( $a ) {
   +    foo();
    }

Example #4
~~~~~~~~~~

With configuration: ``['space' => 'spaces']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo($bar, $baz)
   +function foo( $bar, $baz )
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

