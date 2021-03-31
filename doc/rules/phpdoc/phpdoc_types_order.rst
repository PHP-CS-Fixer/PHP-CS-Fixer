===========================
Rule ``phpdoc_types_order``
===========================

Sorts PHPDoc types.

Configuration
-------------

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

The sorting algorithm to apply.

Allowed values: ``'alpha'``, ``'none'``

Default value: ``'alpha'``

``null_adjustment``
~~~~~~~~~~~~~~~~~~~

Forces the position of ``null`` (overrides ``sort_algorithm``).

Allowed values: ``'always_first'``, ``'always_last'``, ``'none'``

Default value: ``'always_first'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param string|null $bar
   + * @param null|string $bar
     */

Example #2
~~~~~~~~~~

With configuration: ``['null_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param null|string $bar
   + * @param string|null $bar
     */

Example #3
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'alpha']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param null|string|int|\Foo $bar
   + * @param null|\Foo|int|string $bar
     */

Example #4
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param null|string|int|\Foo $bar
   + * @param \Foo|int|string|null $bar
     */

Example #5
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'alpha', 'null_adjustment' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param null|string|int|\Foo $bar
   + * @param \Foo|int|null|string $bar
     */

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_types_order`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_types_order`` rule with the config below:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``
