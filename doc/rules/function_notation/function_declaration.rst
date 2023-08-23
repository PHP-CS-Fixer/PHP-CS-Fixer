=============================
Rule ``function_declaration``
=============================

Spaces should be properly placed in a function declaration.

Configuration
-------------

``closure_function_spacing``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Spacing to use before open parenthesis for closures.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'one'``

``closure_fn_spacing``
~~~~~~~~~~~~~~~~~~~~~~

Spacing to use before open parenthesis for short arrow functions.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'one'``

``trailing_comma_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether trailing commas are allowed in single line signatures.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class Foo
    {
   -    public static function  bar   ( $baz , $foo )
   +    public static function bar($baz , $foo)
        {
            return false;
        }
    }

   -function  foo  ($bar, $baz)
   +function foo($bar, $baz)
    {
        return false;
    }

Example #2
~~~~~~~~~~

With configuration: ``['closure_function_spacing' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$f = function () {};
   +$f = function() {};

Example #3
~~~~~~~~~~

With configuration: ``['closure_fn_spacing' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$f = fn () => null;
   +$f = fn() => null;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

