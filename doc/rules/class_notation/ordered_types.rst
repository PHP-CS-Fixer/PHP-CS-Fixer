======================
Rule ``ordered_types``
======================

Sort union types and intersection types using configured order.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``case_sensitive``,
``false_adjustment``, ``null_adjustment``, ``sort_algorithm``.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

``false_adjustment``
~~~~~~~~~~~~~~~~~~~~

Forces the position of ``false`` (overrides ``sort_algorithm``). When both
``false_adjustment`` and ``null_adjustment`` are set to the same value, ``null``
is sorted further out (e.g. ``int|false|null`` rather than ``int|null|false``).

Allowed values: ``'always_first'``, ``'always_last'`` and ``'none'``

Default value: ``'none'``

``null_adjustment``
~~~~~~~~~~~~~~~~~~~

Forces the position of ``null`` (overrides ``sort_algorithm``).

Allowed values: ``'always_first'``, ``'always_last'`` and ``'none'``

Default value: ``'always_first'``

Default value (future-mode): ``'always_last'``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

Whether the types should be sorted alphabetically, or not sorted.

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
    try {
        cache()->save($foo);
   -} catch (\RuntimeException|CacheException $e) {
   +} catch (CacheException|\RuntimeException $e) {
        logger($e);

        throw $e;
    }

Example #2
~~~~~~~~~~

With configuration: ``['case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Foo
    {
   -    public function bar(\Aaa|\AA $foo): string|int;
   +    public function bar(\AA|\Aaa $foo): int|string;
    }

Example #3
~~~~~~~~~~

With configuration: ``['null_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Foo
    {
   -    public function bar(null|string|int $foo): string|int;
   +    public function bar(int|string|null $foo): int|string;

   -    public function foo(\Stringable&\Countable $obj): int;
   +    public function foo(\Countable&\Stringable $obj): int;
    }

Example #4
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'none', 'null_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Bar
    {
   -    public function bar(null|string|int $foo): string|int;
   +    public function bar(string|int|null $foo): string|int;
    }

Example #5
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'none', 'false_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Bar
    {
   -    public function bar(false|string|int $foo): string|int;
   +    public function bar(string|int|false $foo): string|int;
    }

Example #6
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'none', 'false_adjustment' => 'always_last', 'null_adjustment' => 'always_last']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Bar
    {
   -    public function bar(null|false|string|int $foo): string|int;
   +    public function bar(string|int|false|null $foo): string|int;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['null_adjustment' => 'always_last']``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\OrderedTypesFixer <./../../../src/Fixer/ClassNotation/OrderedTypesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\OrderedTypesFixerTest <./../../../tests/Fixer/ClassNotation/OrderedTypesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
