===============================
Rule ``single_line_empty_body``
===============================

Empty body of class, interface, trait, enum or function must be abbreviated as
``{}`` and placed on the same line as the previous symbol, separated by a single
space.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php function foo(
        int $x
   -)
   -{
   -}
   +) {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\SingleLineEmptyBodyFixer <./../../../src/Fixer/Basic/SingleLineEmptyBodyFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\SingleLineEmptyBodyFixerTest <./../../../tests/Fixer/Basic/SingleLineEmptyBodyFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
