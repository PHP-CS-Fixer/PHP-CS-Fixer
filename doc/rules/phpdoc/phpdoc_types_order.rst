===========================
Rule ``phpdoc_types_order``
===========================

Sorts PHPDoc types.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

``null_adjustment``
~~~~~~~~~~~~~~~~~~~

Forces the position of ``null`` (overrides ``sort_algorithm``).

Allowed values: ``'always_first'``, ``'always_last'`` and ``'none'``

Default value: ``'always_first'``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

The sorting algorithm to apply.

Allowed values: ``'alpha'`` and ``'none'``

Default value: ``'alpha'``

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

Example #6
~~~~~~~~~~

With configuration: ``['case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param Aaa|AA $bar
   + * @param AA|Aaa $bar
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``


Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesOrderFixer <./../src/Fixer/Phpdoc/PhpdocTypesOrderFixer.php>`_
