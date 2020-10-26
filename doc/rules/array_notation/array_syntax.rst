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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``array_syntax`` rule with the config below:

  ``['syntax' => 'short']``
