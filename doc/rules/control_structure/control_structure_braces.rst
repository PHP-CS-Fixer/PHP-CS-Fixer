=================================
Rule ``control_structure_braces``
=================================

The body of each control structure MUST be enclosed within braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (foo()) echo 'Hello!';
   +if (foo()) { echo 'Hello!'; }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
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

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\ControlStructureBracesFixer <./../../../src/Fixer/ControlStructure/ControlStructureBracesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\ControlStructureBracesFixerTest <./../../../tests/Fixer/ControlStructure/ControlStructureBracesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
