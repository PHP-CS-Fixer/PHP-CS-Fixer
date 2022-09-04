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

Default value: ``'short'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -array(1,2);
   +[1,2];

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -[1,2];
   +array(1,2);

Rule sets
---------

The rule is part of the following rule sets:

@PHP54Migration
  Using the `@PHP54Migration <./../../ruleSets/PHP54Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP70Migration
  Using the `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP71Migration
  Using the `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``array_syntax`` rule with the default config.
