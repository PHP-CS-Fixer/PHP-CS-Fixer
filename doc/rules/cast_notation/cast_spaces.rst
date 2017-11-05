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
   @@ -1,3 +1,3 @@
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
   @@ -1,3 +1,3 @@
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
   @@ -1,3 +1,3 @@
    <?php
   -$bar = ( string )  $a;
   -$foo = (int) $b;
   +$bar = (string)$a;
   +$foo = (int)$b;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``cast_spaces`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``cast_spaces`` rule with the default config.
