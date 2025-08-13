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
   -$a = $b ||
   -    $c;
   -$d = $e +
   -    $f;
   +$a = $b
   +    || $c;
   +$d = $e
   +    + $f;

Example #2
~~~~~~~~~~

With configuration: ``['only_booleans' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = $b ||
   -    $c;
   +$a = $b
   +    || $c;
    $d = $e +
        $f;

Example #3
~~~~~~~~~~

With configuration: ``['position' => 'end']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = $b
   -    || $c;
   -$d = $e
   -    + $f;
   +$a = $b ||
   +    $c;
   +$d = $e +
   +    $f;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['only_booleans' => true]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\OperatorLinebreakFixer <./../../../src/Fixer/Operator/OperatorLinebreakFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\OperatorLinebreakFixerTest <./../../../tests/Fixer/Operator/OperatorLinebreakFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
