========================================
Rule ``no_trailing_comma_in_singleline``
========================================

If a list of values separated by a comma is contained on a single line, then the
last item MUST NOT have a trailing comma.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``elements``.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Which elements to fix.

Allowed values: a subset of ``['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']``

Default value: ``['arguments', 'array', 'array_destructuring', 'group_import']``

Default value (future-mode): ``['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo($a,);
   -$foo = array(1,);
   -[$foo, $bar,] = $array;
   -use a\{ClassA, ClassB,};
   +foo($a);
   +$foo = array(1);
   +[$foo, $bar] = $array;
   +use a\{ClassA, ClassB};

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['array_destructuring']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    foo($a,);
   -[$foo, $bar,] = $array;
   +[$foo, $bar] = $array;

Example #3
~~~~~~~~~~

With configuration: ``['elements' => ['attribute_arguments']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[Foo(1, 2,)]
   +#[Foo(1, 2)]
    function bar() {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['elements' => ['arguments', 'array', 'array_destructuring', 'attribute_arguments', 'group_import']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\NoTrailingCommaInSinglelineFixer <./../../../src/Fixer/Basic/NoTrailingCommaInSinglelineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\NoTrailingCommaInSinglelineFixerTest <./../../../tests/Fixer/Basic/NoTrailingCommaInSinglelineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
