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

``ensure_fully_multiline``
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use option ``on_multiline`` instead.

ensure every argument of a multiline argument list is on its own line

Allowed types: ``bool``

Default value: ``false``

``on_multiline``
~~~~~~~~~~~~~~~~

Defines how to handle function arguments lists that contain newlines.

Allowed values: ``'ensure_fully_multiline'``, ``'ensure_single_line'``, ``'ignore'``

Default value: ``'ignore'``

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
   @@ -1,3 +1,3 @@
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
   @@ -1,3 +1,3 @@
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
   @@ -1,3 +1,3 @@
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
   @@ -1,5 +1,10 @@
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
   @@ -1,10 +1,3 @@
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
   @@ -1,7 +1,12 @@
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
   @@ -1,7 +1,12 @@
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
   @@ -2,7 +2,6 @@
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

@PSR2
  Using the ``@PSR2`` rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``

@Symfony
  Using the ``@Symfony`` rule set will enable the ``method_argument_space`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``method_argument_space`` rule with the config below:

  ``['on_multiline' => 'ensure_fully_multiline']``
