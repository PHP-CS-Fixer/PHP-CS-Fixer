====================
Rule ``line_ending``
====================

All PHP files must use same line ending.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php $b = " $a ^M
   - 123"; $a = <<<TEST^M
   -AAAAA ^M
   - |^M
   + 123"; $a = <<<TEST
   +AAAAA 
   + |
    TEST;

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

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\LineEndingFixer <./../../../src/Fixer/Whitespace/LineEndingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\LineEndingFixerTest <./../../../tests/Fixer/Whitespace/LineEndingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
