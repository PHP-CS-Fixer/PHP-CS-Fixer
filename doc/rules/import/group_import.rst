=====================
Rule ``group_import``
=====================

There MUST be group use for the same namespaces.

Configuration
-------------

``group_types``
~~~~~~~~~~~~~~~

Defines the order of import types.

Allowed types: ``list<string>``

Default value: ``['classy', 'functions', 'constants']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -use Foo\Bar;
   -use Foo\Baz;
   +use Foo\{Bar, Baz};

Example #2
~~~~~~~~~~

With configuration: ``['group_types' => ['classy']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -use A\Foo;
    use function B\foo;
   -use A\Bar;
   +use A\{Bar, Foo};
    use function B\bar;

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\GroupImportFixer <./../../../src/Fixer/Import/GroupImportFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\GroupImportFixerTest <./../../../tests/Fixer/Import/GroupImportFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
