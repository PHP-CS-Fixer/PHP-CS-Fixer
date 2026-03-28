====================
Rule ``void_return``
====================

Add ``void`` return type to functions with missing or empty return statements,
but priority is given to ``@return`` annotations.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Modifies the signature of functions.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``fix_lambda``.

Configuration
-------------

``fix_lambda``
~~~~~~~~~~~~~~

Whether to fix lambda functions as well.

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

    class Foo
    {
   -    public function hello()
   +    public function hello(): void
        {
   -        $hello = function($a) { echo 'Hello '.$a; };
   +        $hello = function($a): void { echo 'Hello '.$a; };
            echo $hello('World');
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['fix_lambda' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class Foo
    {
   -    public function hello()
   +    public function hello(): void
        {
            $hello = function($a) { echo 'Hello '.$a; };
            echo $hello('World');
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_
- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ *(deprecated)*
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\VoidReturnFixer <./../../../src/Fixer/FunctionNotation/VoidReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\VoidReturnFixerTest <./../../../tests/Fixer/FunctionNotation/VoidReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
