====================
Rule ``list_syntax``
====================

List (``array`` destructuring) assignment should be declared using the
configured syntax. Requires PHP >= 7.1.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` ``list`` syntax.

Allowed values: ``'long'``, ``'short'``

Default value: ``'long'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -[$sample] = $array;
   +list($sample) = $array;

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'short']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -list($sample) = $array;
   +[$sample] = $array;

Rule sets
---------

The rule is part of the following rule sets:

@PHP71Migration
  Using the `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ rule set will enable the ``list_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``list_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``list_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``list_syntax`` rule with the config below:

  ``['syntax' => 'short']``
