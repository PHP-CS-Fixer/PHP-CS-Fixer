========================
Rule ``new_with_braces``
========================

All instances created with ``new`` keyword must (not) be followed by braces.

Configuration
-------------

``named_class``
~~~~~~~~~~~~~~~

Whether named classes should be followed by parentheses.

Allowed types: ``bool``

Default value: ``true``

``anonymous_class``
~~~~~~~~~~~~~~~~~~~

Whether anonymous classes should be followed by parentheses.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$x = new X;
   -$y = new class {};
   +$x = new X();
   +$y = new class() {};

Example #2
~~~~~~~~~~

With configuration: ``['anonymous_class' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$y = new class() {};
   +$y = new class {};

Example #3
~~~~~~~~~~

With configuration: ``['named_class' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$x = new X();
   +$x = new X;

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``new_with_braces`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``new_with_braces`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``new_with_braces`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``new_with_braces`` rule with the default config.
