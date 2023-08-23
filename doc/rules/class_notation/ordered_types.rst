======================
Rule ``ordered_types``
======================

Sort union types and intersection types using configured order.

Configuration
-------------

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

Whether the types should be sorted alphabetically, or not sorted.

Allowed values: ``'alpha'`` and ``'none'``

Default value: ``'alpha'``

``null_adjustment``
~~~~~~~~~~~~~~~~~~~

Forces the position of ``null`` (overrides ``sort_algorithm``).

Allowed values: ``'always_first'``, ``'always_last'`` and ``'none'``

Default value: ``'always_first'``

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

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
