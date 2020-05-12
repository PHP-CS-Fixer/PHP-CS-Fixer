====================
Rule ``cast_spaces``
====================

A single space or none should be between cast and variable.

Configuration
-------------

``space``
~~~~~~~~~

spacing to apply between cast and variable.

Allowed values: ``'none'``, ``'single'``

Default value: ``'single'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int)$b;
   +$bar = (string) $a;
   +$foo = (int) $b;

Example #2
~~~~~~~~~~

With configuration: ``['space' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int)$b;
   +$bar = (string) $a;
   +$foo = (int) $b;

Example #3
~~~~~~~~~~

With configuration: ``['space' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int) $b;
   +$bar = (string)$a;
   +$foo = (int)$b;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``cast_spaces`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``cast_spaces`` rule with the default config.
