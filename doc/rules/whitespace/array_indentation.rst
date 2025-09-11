==========================
Rule ``array_indentation``
==========================

Each element of an array must be indented exactly once.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $foo = [
   -   'bar' => [
   -    'baz' => true,
   -  ],
   +    'bar' => [
   +        'baz' => true,
   +    ],
    ];

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\ArrayIndentationFixer <./../../../src/Fixer/Whitespace/ArrayIndentationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\ArrayIndentationFixerTest <./../../../tests/Fixer/Whitespace/ArrayIndentationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
