=============================
Rule ``function_declaration``
=============================

Spaces should be properly placed in a function declaration.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``closure_fn_spacing``,
``closure_function_spacing``, ``trailing_comma_single_line``.

Configuration
-------------

``closure_fn_spacing``
~~~~~~~~~~~~~~~~~~~~~~

Spacing to use before open parenthesis for short arrow functions.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'one'``

``closure_function_spacing``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Spacing to use before open parenthesis for closures.

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

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['closure_fn_spacing' => 'none']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['closure_fn_spacing' => 'none']``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)* with config:

  ``['closure_fn_spacing' => 'one']``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['closure_fn_spacing' => 'none']``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['closure_fn_spacing' => 'none']``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['closure_fn_spacing' => 'none']``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['closure_fn_spacing' => 'none']``

- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\FunctionDeclarationFixer <./../../../src/Fixer/FunctionNotation/FunctionDeclarationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\FunctionDeclarationFixerTest <./../../../tests/Fixer/FunctionNotation/FunctionDeclarationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
