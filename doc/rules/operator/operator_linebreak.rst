===========================
Rule ``operator_linebreak``
===========================

Operators - when multiline - must always be at the beginning or at the end of
the line.

Configuration
-------------

``only_booleans``
~~~~~~~~~~~~~~~~~

Whether to limit operators to only boolean ones.

Allowed types: ``bool``

Default value: ``false``

``position``
~~~~~~~~~~~~

Whether to place operators at the beginning or at the end of the line.

Allowed values: ``'beginning'`` and ``'end'``

Default value: ``'beginning'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function foo() {
   -    return $bar ||
   -        $baz;
   +    return $bar
   +        || $baz;
    }

Example #2
~~~~~~~~~~

With configuration: ``['position' => 'end']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function foo() {
   -    return $bar
   -        || $baz;
   +    return $bar ||
   +        $baz;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['only_booleans' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['only_booleans' => true]``


References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\OperatorLinebreakFixer <./../../../src/Fixer/Operator/OperatorLinebreakFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\OperatorLinebreakFixerTest <./../../../tests/Fixer/Operator/OperatorLinebreakFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
