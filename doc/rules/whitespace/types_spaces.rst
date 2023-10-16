=====================
Rule ``types_spaces``
=====================

A single space or none should be around union type and intersection type
operators.

Configuration
-------------

``space``
~~~~~~~~~

Spacing to apply around union type and intersection type operators.

Allowed values: ``'none'`` and ``'single'``

Default value: ``'none'``

``space_multiple_catch``
~~~~~~~~~~~~~~~~~~~~~~~~

Spacing to apply around type operator when catching exceptions of multiple
types, use ``null`` to follow the value configured for ``space``.

Allowed values: ``'none'``, ``'single'`` and ``null``

Default value: ``null``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    try
    {
        new Foo();
   -} catch (ErrorA | ErrorB $e) {
   +} catch (ErrorA|ErrorB $e) {
    echo'error';}

Example #2
~~~~~~~~~~

With configuration: ``['space' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    try
    {
        new Foo();
   -} catch (ErrorA|ErrorB $e) {
   +} catch (ErrorA | ErrorB $e) {
    echo'error';}

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int | string $x)
   +function foo(int|string $x)
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

