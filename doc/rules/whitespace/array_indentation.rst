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

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Whitespace\\ArrayIndentationFixer <./../../../src/Fixer/Whitespace/ArrayIndentationFixer.php>`_
