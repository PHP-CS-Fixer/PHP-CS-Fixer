========================
Rule ``multiline_array``
========================

Arrays with at least a configured number of elements MUST be split across
multiple lines, with one element per line.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``min_items``,
``variables_only``.

Configuration
-------------

``min_items``
~~~~~~~~~~~~~

Minimum number of elements an array must contain to be split across multiple
lines.

Allowed types: ``int``

Default value: ``2``

``variables_only``
~~~~~~~~~~~~~~~~~~

Whether to only split arrays in an assignment-like position (assigned to a
variable, returned, or yielded), leaving e.g. arrays passed as arguments
untouched.

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
   -$foo = ['a', 'b', 'c'];
   +$foo = [
   +    'a',
   +    'b',
   +    'c'
   +];

Example #2
~~~~~~~~~~

With configuration: ``['min_items' => 3]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $foo = ['a', 'b'];
   -$bar = ['a', 'b', 'c'];
   +$bar = [
   +    'a',
   +    'b',
   +    'c'
   +];

Example #3
~~~~~~~~~~

With configuration: ``['variables_only' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    bar(['a', 'b']);
   -$foo = ['a', 'b'];
   +$foo = [
   +    'a',
   +    'b'
   +];

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\MultilineArrayFixer <./../../../src/Fixer/ArrayNotation/MultilineArrayFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\MultilineArrayFixerTest <./../../../tests/Fixer/ArrayNotation/MultilineArrayFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
