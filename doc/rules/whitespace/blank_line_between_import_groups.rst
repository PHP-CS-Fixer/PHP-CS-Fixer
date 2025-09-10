=========================================
Rule ``blank_line_between_import_groups``
=========================================

Putting blank lines between ``use`` statement groups.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use function AAC;
   +
    use const AAB;
   +
    use AAA;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use const AAAA;
    use const BBB;
   +
    use Bar;
    use AAC;
    use Acme;
   +
    use function CCC\AA;
    use function DDD;

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use const BBB;
    use const AAAA;
   +
    use Acme;
    use AAC;
    use Bar;
   +
    use function DDD;
    use function CCC\AA;

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use const AAAA;
    use const BBB;
   +
    use Acme;
   +
    use function DDD;
   +
    use AAC;
   +
    use function CCC\AA;
   +
    use Bar;

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

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\BlankLineBetweenImportGroupsFixer <./../../../src/Fixer/Whitespace/BlankLineBetweenImportGroupsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\BlankLineBetweenImportGroupsFixerTest <./../../../tests/Fixer/Whitespace/BlankLineBetweenImportGroupsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
