==================================
Rule ``single_line_after_imports``
==================================

Each namespace use MUST go on its own line and there MUST be one blank line
after the use statements block.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace Foo;

    use Bar;
    use Baz;
   +
    final class Example
    {
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace Foo;

    use Bar;
    use Baz;

   -
    final class Example
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\SingleLineAfterImportsFixer <./../../../src/Fixer/Import/SingleLineAfterImportsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\SingleLineAfterImportsFixerTest <./../../../tests/Fixer/Import/SingleLineAfterImportsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
