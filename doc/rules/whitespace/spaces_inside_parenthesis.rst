==================================
Rule ``spaces_inside_parenthesis``
==================================

Parenthesis must be declared using the configured syntax.

Configuration
-------------

``space``
~~~~~~~~~

whether to have ``spaces`` or ``none`` spaces inside parenthesis.

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
