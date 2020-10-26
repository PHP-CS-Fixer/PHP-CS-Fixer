========================
Rule ``increment_style``
========================

Pre- or post-increment and decrement operators should be used if possible.

Configuration
-------------

``style``
~~~~~~~~~

Whether to use pre- or post-increment and decrement operators.

Allowed values: ``'post'``, ``'pre'``

Default value: ``'pre'``

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
   -$a++;
   -$b--;
   +++$a;
   +--$b;

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'post']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -++$a;
   ---$b;
   +$a++;
   +$b--;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``increment_style`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``increment_style`` rule with the default config.
