==============================
Rule ``curly_braces_position``
==============================

Curly braces must be placed as configured.

Configuration
-------------

``control_structures_opening_brace``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

the position of the opening brace of control structures body.

Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``

Default value: ``'same_line'``

``functions_opening_brace``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

the position of the opening brace of functions body.

Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``

Default value: ``'next_line_unless_newline_at_signature_end'``

``anonymous_functions_opening_brace``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

the position of the opening brace of anonymous functions body.

Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``

Default value: ``'same_line'``

``classes_opening_brace``
~~~~~~~~~~~~~~~~~~~~~~~~~

the position of the opening brace of classes body.

Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``

Default value: ``'next_line_unless_newline_at_signature_end'``

``anonymous_classes_opening_brace``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

the position of the opening brace of anonymous classes body.

Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``

Default value: ``'same_line'``

``allow_single_line_empty_anonymous_classes``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

allow anonymous classes to have opening and closing braces on the same line.

Allowed types: ``bool``

Default value: ``true``

``allow_single_line_anonymous_functions``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

allow anonymous functions to have opening and closing braces on the same line.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -class Foo {
   +class Foo
   +{
    }

   -function foo() {
   +function foo()
   +{
    }

   -$foo = function()
   -{
   +$foo = function() {
    };

   -if (foo())
   -{
   +if (foo()) {
        bar();
    }

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = new class
   -{
   +$foo = new class {
    };

Example #3
~~~~~~~~~~

With configuration: ``['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (foo()) {
   +if (foo())
   +{
        bar();
    }

Example #4
~~~~~~~~~~

With configuration: ``['functions_opening_brace' => 'same_line']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo()
   -{
   +function foo() {
    }

Example #5
~~~~~~~~~~

With configuration: ``['anonymous_functions_opening_brace' => 'next_line_unless_newline_at_signature_end']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = function () {
   +$foo = function ()
   +{
    };

Example #6
~~~~~~~~~~

With configuration: ``['classes_opening_brace' => 'same_line']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -class Foo
   -{
   +class Foo {
    }

Example #7
~~~~~~~~~~

With configuration: ``['anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = new class {
   +$foo = new class
   +{
    };

Example #8
~~~~~~~~~~

With configuration: ``['allow_single_line_empty_anonymous_classes' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $foo = new class { };
   -$bar = new class { private $baz; };
   +$bar = new class {
   +private $baz;
   +};

Example #9
~~~~~~~~~~

With configuration: ``['allow_single_line_anonymous_functions' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $foo = function () { return true; };
   -$bar = function () { $result = true;
   -    return $result; };
   +$bar = function () {
   +$result = true;
   +    return $result;
   +};
