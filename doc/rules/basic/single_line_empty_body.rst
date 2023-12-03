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
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Basic\\SingleLineEmptyBodyFixer <./../../../src/Fixer/Basic/SingleLineEmptyBodyFixer.php>`_
