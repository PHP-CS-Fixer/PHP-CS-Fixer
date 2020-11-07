=====================
Rule ``concat_space``
=====================

Concatenation should be spaced according configuration.

Configuration
-------------

``spacing``
~~~~~~~~~~~

Spacing to apply around concatenation operator.

Allowed values: ``'none'``, ``'one'``

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
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar'. 3 .'baz'.'qux';

Example #2
~~~~~~~~~~

With configuration: ``['spacing' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar'. 3 .'baz'.'qux';

Example #3
~~~~~~~~~~

With configuration: ``['spacing' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar' . 3 . 'baz' . 'qux';

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``concat_space`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``concat_space`` rule with the default config.
