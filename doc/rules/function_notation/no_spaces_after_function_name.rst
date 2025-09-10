======================================
Rule ``no_spaces_after_function_name``
======================================

When making a method or function call, there MUST NOT be a space between the
method or function name and the opening parenthesis.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -strlen ('Hello World!');
   -foo (test (3));
   -exit  (1);
   -$func ();
   +strlen('Hello World!');
   +foo(test(3));
   +exit(1);
   +$func();

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
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NoSpacesAfterFunctionNameFixer <./../../../src/Fixer/FunctionNotation/NoSpacesAfterFunctionNameFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NoSpacesAfterFunctionNameFixerTest <./../../../tests/Fixer/FunctionNotation/NoSpacesAfterFunctionNameFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
