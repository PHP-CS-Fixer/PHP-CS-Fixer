==============================
Rule ``method_argument_space``
==============================

In method arguments and method call, there MUST NOT be a space before each comma
and there MUST be one space after each comma. Argument lists MAY be split across
multiple lines, where each subsequent line is indented once. When doing so, the
first item in the list MUST be on the next line, and there MUST be only one
argument per line.

Description
-----------

This fixer covers rules defined in PSR2 ¶4.4, ¶4.6.

Configuration
-------------

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether the whitespace between heredoc end and comma should be removed.

Allowed types: ``bool``

Default value: ``false``

``attribute_placement``
~~~~~~~~~~~~~~~~~~~~~~~

Defines how to handle argument attributes when function definition is multiline.

Allowed values: ``'ignore'``, ``'same_line'`` and ``'standalone'``

Default value: ``'standalone'``

``keep_multiple_spaces_after_comma``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether keep multiple spaces after comma.

Allowed types: ``bool``

Default value: ``false``

``on_multiline``
~~~~~~~~~~~~~~~~

Defines how to handle function arguments lists that contain newlines.

Allowed values: ``'ensure_fully_multiline'``, ``'ensure_single_line'`` and ``'ignore'``

Default value: ``'ensure_fully_multiline'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,$b=20,$c=30) {}
   -sample(1,  2);
   +function sample($a=10, $b=20, $c=30) {}
   +sample(1, 2);

Example #2
~~~~~~~~~~

With configuration: ``['keep_multiple_spaces_after_comma' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,$b=20,$c=30) {}
   -sample(1,  2);
   +function sample($a=10, $b=20, $c=30) {}
   +sample(1, 2);

Example #3
~~~~~~~~~~

With configuration: ``['keep_multiple_spaces_after_comma' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,$b=20,$c=30) {}
   +function sample($a=10, $b=20, $c=30) {}
    sample(1,  2);

Example #4
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,
   -    $b=20,$c=30) {}
   -sample(1,
   -    2);
   +function sample(
   +    $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(
   +    1,
   +    2
   +);

Example #5
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_single_line']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(
   -    $a=10,
   -    $b=20,
   -    $c=30
   -) {}
   -sample(
   -    1,
   -    2
   -);
   +function sample($a=10, $b=20, $c=30) {}
   +sample(1, 2);

Example #6
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline', 'keep_multiple_spaces_after_comma' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,
   -    $b=20,$c=30) {}
   -sample(1,  
   -    2);
   +function sample(
   +    $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(
   +    1,
   +    2
   +);
    sample('foo',    'foobarbaz', 'baz');
    sample('foobar', 'bar',       'baz');

Example #7
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline', 'keep_multiple_spaces_after_comma' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample($a=10,
   -    $b=20,$c=30) {}
   -sample(1,  
   -    2);
   -sample('foo',    'foobarbaz', 'baz');
   -sample('foobar', 'bar',       'baz');
   +function sample(
   +    $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(
   +    1,
   +    2
   +);
   +sample('foo', 'foobarbaz', 'baz');
   +sample('foobar', 'bar', 'baz');

Example #8
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline', 'attribute_placement' => 'ignore']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(#[Foo] #[Bar] $a=10,
   -    $b=20,$c=30) {}
   -sample(1,  2);
   +function sample(
   +    #[Foo] #[Bar] $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(1, 2);

Example #9
~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline', 'attribute_placement' => 'same_line']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(#[Foo]
   -    #[Bar]
   -    $a=10,
   -    $b=20,$c=30) {}
   -sample(1,  2);
   +function sample(
   +    #[Foo] #[Bar] $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(1, 2);

Example #10
~~~~~~~~~~~

With configuration: ``['on_multiline' => 'ensure_fully_multiline', 'attribute_placement' => 'standalone']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(#[Foo] #[Bar] $a=10,
   -    $b=20,$c=30) {}
   -sample(1,  2);
   +function sample(
   +    #[Foo]
   +    #[Bar]
   +    $a=10,
   +    $b=20,
   +    $c=30
   +) {}
   +sample(1, 2);

Example #11
~~~~~~~~~~~

With configuration: ``['after_heredoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    sample(
        <<<EOD
            foo
   -        EOD
   -    ,
   +        EOD,
        'bar'
    );

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['attribute_placement' => 'ignore', 'on_multiline' => 'ensure_fully_multiline']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
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

- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['attribute_placement' => 'ignore', 'on_multiline' => 'ensure_fully_multiline']``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['attribute_placement' => 'ignore', 'on_multiline' => 'ensure_fully_multiline']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['after_heredoc' => true, 'on_multiline' => 'ensure_fully_multiline']``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['after_heredoc' => true, 'on_multiline' => 'ignore']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\MethodArgumentSpaceFixer <./../../../src/Fixer/FunctionNotation/MethodArgumentSpaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\MethodArgumentSpaceFixerTest <./../../../tests/Fixer/FunctionNotation/MethodArgumentSpaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
