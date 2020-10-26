================================
Rule ``declare_equal_normalize``
================================

Equal sign in declare statement should be surrounded by spaces or not following
configuration.

Configuration
-------------

``space``
~~~~~~~~~

Spacing to apply around the equal sign.

Allowed values: ``'none'``, ``'single'``

Default value: ``'none'``

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
   -declare(ticks =  1);
   +declare(ticks=1);

Example #2
~~~~~~~~~~

With configuration: ``['space' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -declare(ticks=1);
   +declare(ticks = 1);

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``declare_equal_normalize`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``declare_equal_normalize`` rule with the default config.
