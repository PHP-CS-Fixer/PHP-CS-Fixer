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

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\TypesSpacesFixer <./../../../src/Fixer/Whitespace/TypesSpacesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\TypesSpacesFixerTest <./../../../tests/Fixer/Whitespace/TypesSpacesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
