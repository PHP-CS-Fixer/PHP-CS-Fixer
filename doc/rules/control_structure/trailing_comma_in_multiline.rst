====================================
Rule ``trailing_comma_in_multiline``
====================================

Multi-line arrays, arguments list, parameters list and ``match`` expressions
must have a trailing comma.

Configuration
-------------

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether a trailing comma should also be placed after heredoc end.

Allowed types: ``bool``

Default value: ``false``

``elements``
~~~~~~~~~~~~

Where to fix multiline trailing comma (PHP >= 8.0 for ``parameters`` and
``match``).

Allowed values: a subset of ``['arguments', 'arrays', 'match', 'parameters']``

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

- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\TrailingCommaInMultilineFixer <./../../../src/Fixer/ControlStructure/TrailingCommaInMultilineFixer.php>`_
