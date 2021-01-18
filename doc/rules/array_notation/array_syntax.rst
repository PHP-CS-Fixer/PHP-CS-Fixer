=====================
Rule ``array_syntax``
=====================

PHP arrays should be declared using the configured syntax.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` array syntax.

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
   @@ -1,2 +1,2 @@
    <?php
   -[1,2];
   +array(1,2);

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'short']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -array(1,2);
   +[1,2];

Rule sets
---------

The rule is part of the following rule sets:

@PHP54Migration
  Using the `@PHP54Migration <./../../ruleSets/PHP54Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP56Migration
  Using the `@PHP56Migration <./../../ruleSets/PHP56Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP70Migration
  Using the `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP71Migration
  Using the `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``
