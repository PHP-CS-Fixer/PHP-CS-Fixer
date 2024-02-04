========================
Rule ``fully_multiline``
========================

Multi-line arrays, arguments list, parameters list, control structures,
``switch`` cases and ``match`` expressions should have one element by line.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Which expression must have one element by line (PHP >= 8.0 for ``parameters``
and ``match``).

Allowed values: a subset of ``['arguments', 'arrays', 'case', 'control_structures', 'match', 'parameters']``

Default value: ``['arrays']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -array(1,
   -    2);
   +array(
   +1,
   +    2
   +);

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['arguments']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo(1,
   -    2);
   +foo(
   +1,
   +    2
   +);

Example #3
~~~~~~~~~~

With configuration: ``['elements' => ['control_structures']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if ($a
   -    && $b) {};
   +if (
   +$a
   +    && $b
   +) {};

Example #4
~~~~~~~~~~

With configuration: ``['elements' => ['case']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
   -    case 0: case 1:
   +    case 0:
   +case 1:
            return null;
        };

Example #5
~~~~~~~~~~

With configuration: ``['elements' => ['parameters']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo($x,
   -    $y)
   +function foo(
   +$x,
   +    $y
   +)
    {
    }

Example #6
~~~~~~~~~~

With configuration: ``['elements' => ['match']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    match($x) {
   -    1 => 1, 2 => 2
   +    1 => 1,
   +2 => 2
    };
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\FullyMultilineFixer <./../../../src/Fixer/ControlStructure/FullyMultilineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\FullyMultilineFixerTest <./../../../tests/Fixer/ControlStructure/FullyMultilineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
