==========================================
Rule ``single_trait_insert_per_statement``
==========================================

Each trait ``use`` must be done as single statement.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Example
    {
   -    use Foo, Bar;
   +    use Foo;use Bar;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\SingleTraitInsertPerStatementFixer <./../../../src/Fixer/ClassNotation/SingleTraitInsertPerStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\SingleTraitInsertPerStatementFixerTest <./../../../tests/Fixer/ClassNotation/SingleTraitInsertPerStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
