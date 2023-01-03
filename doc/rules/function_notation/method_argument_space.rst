==============================
Rule ``method_argument_space``
==============================

In method arguments and method call, there MUST NOT be a space before each comma
and there MUST be one space after each comma. Argument lists MAY be split across
multiple lines, where each subsequent line is indented once. When doing so, the
first item in the list MUST be on the next line, and there MUST be only one
argument per line.

Configuration
-------------

``keep_multiple_spaces_after_comma``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether keep multiple spaces after comma.

Allowed types: ``bool``

Default value: ``false``

``on_multiline``
~~~~~~~~~~~~~~~~

Defines how to handle function arguments lists that contain newlines.

Allowed values: ``'ensure_fully_multiline'``, ``'ensure_single_line'``, ``'ignore'``

Default value: ``'ensure_fully_multiline'``

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether the whitespace between heredoc end and comma should be removed.

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

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['after_heredoc' => true]``

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['after_heredoc' => true]``

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['after_heredoc' => true]``

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['after_heredoc' => true]``

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['after_heredoc' => true]``

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ignore']``
