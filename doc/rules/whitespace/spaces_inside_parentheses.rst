==================================
Rule ``spaces_inside_parentheses``
==================================

Parentheses must be declared using the configured whitespace.

Description
-----------

By default there are not any additional spaces inside parentheses, however with
``space=single`` configuration option whitespace inside parentheses will be
unified to single space.

Configuration
-------------

``space``
~~~~~~~~~

Whether to have ``single`` or ``none`` space inside parentheses.

Allowed values: ``'none'`` and ``'single'``

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

With configuration: ``['space' => 'single']``.

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

With configuration: ``['space' => 'single']``.

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
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\SpacesInsideParenthesesFixer <./../../../src/Fixer/Whitespace/SpacesInsideParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\SpacesInsideParenthesesFixerTest <./../../../tests/Fixer/Whitespace/SpacesInsideParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
