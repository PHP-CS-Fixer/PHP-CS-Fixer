=========================
Rule ``no_break_comment``
=========================

There must be a comment when fall-through is intentional in a non-empty case
body.

Description
-----------

Adds a "no break" comment before fall-through cases, and removes it if there is
no fall-through.

Configuration
-------------

``comment_text``
~~~~~~~~~~~~~~~~

The text to use in the added comment and to detect it.

Allowed types: ``string``

Default value: ``'no break'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
        case 1:
            foo();
   +        // no break
        case 2:
            bar();
   -        // no break
            break;
        case 3:
            baz();
    }

Example #2
~~~~~~~~~~

With configuration: ``['comment_text' => 'some comment']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
        case 1:
            foo();
   +        // some comment
        case 2:
            foo();
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
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

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoBreakCommentFixer <./../../../src/Fixer/ControlStructure/NoBreakCommentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoBreakCommentFixerTest <./../../../tests/Fixer/ControlStructure/NoBreakCommentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
