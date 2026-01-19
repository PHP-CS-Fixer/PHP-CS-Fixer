=============================================
Rule ``no_line_break_around_binary_operator``
=============================================

Binary operators should be on the same line as their operands.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``ignored_operators``.

Configuration
-------------

``ignored_operators``
~~~~~~~~~~~~~~~~~~~~~

List of binary operators that will be ignored during code analysis.

Allowed types: ``list<string>``

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

Example #2
~~~~~~~~~~

With configuration: ``['ignored_operators' => ['.']]``.

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

    $var3 = [
   -    'foo'
   -        => 'bar',
   -    'bar'
   -        => 'baz',
   +    'foo' => 'bar',
   +    'bar' => 'baz',
    ];

    $var4 = 'Some text'
        . 'Second text';

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NoLineBreakAroundBinaryOperatorFixer <./../../../src/Fixer/Operator/NoLineBreakAroundBinaryOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NoLineBreakAroundBinaryOperatorFixerTest <./../../../tests/Fixer/Operator/NoLineBreakAroundBinaryOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
