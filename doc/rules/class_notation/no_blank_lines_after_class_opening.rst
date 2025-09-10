===========================================
Rule ``no_blank_lines_after_class_opening``
===========================================

There should be no empty lines after class opening brace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Sample
    {
   -
        protected function foo()
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\NoBlankLinesAfterClassOpeningFixer <./../../../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\NoBlankLinesAfterClassOpeningFixerTest <./../../../tests/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
