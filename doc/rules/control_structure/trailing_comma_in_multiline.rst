====================================
Rule ``trailing_comma_in_multiline``
====================================

Arguments lists, array destructuring lists, arrays that are multi-line,
``match``-lines and parameters lists must have a trailing comma.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``after_heredoc``,
``elements``.

Configuration
-------------

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether a trailing comma should also be placed after heredoc end.

Allowed types: ``bool``

Default value: ``false``

Default value (future-mode): ``true``

``elements``
~~~~~~~~~~~~

Where to fix multiline trailing comma (PHP >= 8.0 for ``parameters`` and
``match``).

Allowed values: a subset of ``['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']``

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
    array(
        1,
   -    2
   +    2,
    );

Example #2
~~~~~~~~~~

With configuration: ``['after_heredoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $x = [
            'foo',
            <<<EOD
                bar
   -            EOD
   +            EOD,
        ];

Example #3
~~~~~~~~~~

With configuration: ``['elements' => ['arguments']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    foo(
        1,
   -    2
   +    2,
    );

Example #4
~~~~~~~~~~

With configuration: ``['elements' => ['parameters']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function foo(
        $x,
   -    $y
   +    $y,
    )
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['array_destructuring', 'arrays']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['array_destructuring', 'arrays', 'match', 'parameters']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\TrailingCommaInMultilineFixer <./../../../src/Fixer/ControlStructure/TrailingCommaInMultilineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\TrailingCommaInMultilineFixerTest <./../../../tests/Fixer/ControlStructure/TrailingCommaInMultilineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
