=================================
Rule ``single_blank_line_at_eof``
=================================

A PHP file without end tag must always end with a single empty line feed.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = 1;
   \ No newline at end of file
   +$a = 1;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = 1;
   -

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
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

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\SingleBlankLineAtEofFixer <./../../../src/Fixer/Whitespace/SingleBlankLineAtEofFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\SingleBlankLineAtEofFixerTest <./../../../tests/Fixer/Whitespace/SingleBlankLineAtEofFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
