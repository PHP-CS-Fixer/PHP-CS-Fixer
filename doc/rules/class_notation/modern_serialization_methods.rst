=====================================
Rule ``modern_serialization_methods``
=====================================

Use new serialization methods ``__serialize`` and ``__unserialize`` instead of
deprecated ones ``__sleep`` and ``__wakeup``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when calling the old methods directly or having logic in the ``__sleep``
and ``__wakeup`` methods.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php class Foo {
   -    public function __sleep() {}
   -    public function __wakeup() {}
   +    public function __serialize() {}
   +    public function __unserialize(array $data) {}
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\ModernSerializationMethodsFixer <./../../../src/Fixer/ClassNotation/ModernSerializationMethodsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ModernSerializationMethodsFixerTest <./../../../tests/Fixer/ClassNotation/ModernSerializationMethodsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
