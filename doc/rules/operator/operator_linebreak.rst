===========================
Rule ``operator_linebreak``
===========================

Operators - when multiline - must always be at the beginning or at the end of
the line.

Configuration
-------------

``only_booleans``
~~~~~~~~~~~~~~~~~

whether to limit operators to only boolean ones

Allowed types: ``bool``

Default value: ``false``

``position``
~~~~~~~~~~~~

whether to place operators at the beginning or at the end of the line

Allowed values: ``'beginning'``, ``'end'``

Default value: ``'beginning'``

``ignored_operators``
~~~~~~~~~~~~~~~~~~~~~

Which operators to ignore

Allowed types: ``array``

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

Example #3
~~~~~~~~~~

With configuration: ``['ignored_operators' => ['->']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,6 +3,6 @@
        $bar->$baz
            ->commit();

   -    return $bar
   -        || $baz;
   +    return $bar ||
   +        $baz;
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``operator_linebreak`` rule with the config below:

  ``['only_booleans' => true]``
