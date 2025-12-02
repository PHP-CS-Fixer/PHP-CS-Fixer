===============
Rule ``braces``
===============

The body of each structure MUST be enclosed by braces. Braces should be properly
placed. Body of braces should be properly indented.

Warnings
--------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``single_space_around_construct``, ``control_structure_braces``,
``control_structure_continuation_position``, ``declare_parentheses``,
``no_multiple_statements_per_line``, ``braces_position``,
``statement_indentation`` and ``no_extra_blank_lines`` instead.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options:
``allow_single_line_anonymous_class_with_empty_body``,
``allow_single_line_closure``, ``position_after_anonymous_constructs``,
``position_after_control_structures``,
``position_after_functions_and_oop_constructs``.

Configuration
-------------

``allow_single_line_anonymous_class_with_empty_body``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether single line anonymous class with empty body notation should be allowed.

Allowed types: ``bool``

Default value: ``false``

``allow_single_line_closure``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether single line lambda notation should be allowed.

Allowed types: ``bool``

Default value: ``false``

``position_after_anonymous_constructs``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether the opening brace should be placed on "next" or "same" line after
anonymous constructs (anonymous classes and lambda functions).

Allowed values: ``'next'`` and ``'same'``

Default value: ``'same'``

``position_after_control_structures``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether the opening brace should be placed on "next" or "same" line after
control structures.

Allowed values: ``'next'`` and ``'same'``

Default value: ``'same'``

``position_after_functions_and_oop_constructs``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether the opening brace should be placed on "next" or "same" line after classy
constructs (non-anonymous classes, interfaces, traits, methods and non-lambda
functions).

Allowed values: ``'next'`` and ``'same'``

Default value: ``'next'``

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
   -    public function bar($baz) {
   -        if ($baz = 900) echo "Hello!";
   +class Foo
   +{
   +    public function bar($baz)
   +    {
   +        if ($baz = 900) {
   +            echo "Hello!";
   +        }

   -        if ($baz = 9000)
   +        if ($baz = 9000) {
                echo "Wait!";
   +        }

   -        if ($baz == true)
   -        {
   +        if ($baz == true) {
                echo "Why?";
   -        }
   -        else
   -        {
   +        } else {
                echo "Ha?";
            }

   -        if (is_array($baz))
   -            foreach ($baz as $b)
   -            {
   +        if (is_array($baz)) {
   +            foreach ($baz as $b) {
                    echo $b;
                }
   +        }
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['allow_single_line_closure' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $positive = function ($item) { return $item >= 0; };
    $negative = function ($item) {
   -                return $item < 0; };
   +    return $item < 0;
   +};

Example #3
~~~~~~~~~~

With configuration: ``['position_after_functions_and_oop_constructs' => 'same']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -class Foo
   -{
   -    public function bar($baz)
   -    {
   -        if ($baz = 900) echo "Hello!";
   +class Foo {
   +    public function bar($baz) {
   +        if ($baz = 900) {
   +            echo "Hello!";
   +        }

   -        if ($baz = 9000)
   +        if ($baz = 9000) {
                echo "Wait!";
   +        }

   -        if ($baz == true)
   -        {
   +        if ($baz == true) {
                echo "Why?";
   -        }
   -        else
   -        {
   +        } else {
                echo "Ha?";
            }

   -        if (is_array($baz))
   -            foreach ($baz as $b)
   -            {
   +        if (is_array($baz)) {
   +            foreach ($baz as $b) {
                    echo $b;
                }
   +        }
        }
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\BracesFixer <./../../../src/Fixer/Basic/BracesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\BracesFixerTest <./../../../tests/Fixer/Basic/BracesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
