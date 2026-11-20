===========================================
Rule ``no_line_break_near_binary_operator``
===========================================

Removes line breaks around binary operators according to the configuration.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``default_strategy``,
``operators``.

Configuration
-------------

``default_strategy``
~~~~~~~~~~~~~~~~~~~~

Default fix strategy.

Allowed values: ``'after'``, ``'around'``, ``'before'`` and ``null``

Default value: ``'after'``

``operators``
~~~~~~~~~~~~~

Dictionary of ``binary operator`` => ``fix strategy`` values that differ from
the default strategy.

Allowed types: ``array<string, ?string>``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $var1
        = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

    $var2
        ??= new stdClass();

   -$var3 =
   -[
   +$var3 = [
        'foo'
            => 'bar',
        'bar'
            => 'baz',
    ];

    $var4 = 'Some text'
        . 'Second text';

Example #2
~~~~~~~~~~

With configuration: ``['default_strategy' => 'before']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$var1
   -    = [
   +$var1 = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

   -$var2
   -    ??= new stdClass();
   +$var2 ??= new stdClass();

    $var3 =
    [
   -    'foo'
   -        => 'bar',
   -    'bar'
   -        => 'baz',
   +    'foo' => 'bar',
   +    'bar' => 'baz',
    ];

   -$var4 = 'Some text'
   -    . 'Second text';
   +$var4 = 'Some text' . 'Second text';

Example #3
~~~~~~~~~~

With configuration: ``['default_strategy' => 'around']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$var1
   -    = [
   +$var1 = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

   -$var2
   -    ??= new stdClass();
   +$var2 ??= new stdClass();

   -$var3 =
   -[
   -    'foo'
   -        => 'bar',
   -    'bar'
   -        => 'baz',
   +$var3 = [
   +    'foo' => 'bar',
   +    'bar' => 'baz',
    ];

   -$var4 = 'Some text'
   -    . 'Second text';
   +$var4 = 'Some text' . 'Second text';

Example #4
~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'around']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $var1
        = [
   -        'foo'
   -            => 'bar',
   -        'bar'
   -            =>
   -            'baz',
   +        'foo' => 'bar',
   +        'bar' => 'baz',
        ];

    $var2
        ??= new stdClass();

    $var4 = 'Some text'
        . 'Second text';

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['operators' => ['=>' => 'around']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['operators' => ['=>' => 'around']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NoLineBreakNearBinaryOperatorFixer <./../../../src/Fixer/Operator/NoLineBreakNearBinaryOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NoLineBreakNearBinaryOperatorFixerTest <./../../../tests/Fixer/Operator/NoLineBreakNearBinaryOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
