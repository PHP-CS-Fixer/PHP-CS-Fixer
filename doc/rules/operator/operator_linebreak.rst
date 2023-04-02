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

Allowed values: ``'beginning'``, ``'end'``

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``operator_linebreak`` rule with the config below:

  ``['only_booleans' => true]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``operator_linebreak`` rule with the config below:

  ``['only_booleans' => true]``
