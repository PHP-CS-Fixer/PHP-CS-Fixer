=================================================
Rule ``phpdoc_readonly_class_comment_to_keyword``
=================================================

Converts readonly comment on classes to the readonly keyword.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

If classes marked with ``@readonly`` annotation were extended anyway, applying
this fixer may break the inheritance for their child classes.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
        <?php
   -    /** @readonly */
   -    class C {
   +    
   +    readonly class C {
        }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\PhpdocReadonlyClassCommentToKeywordFixer <./../../../src/Fixer/ClassNotation/PhpdocReadonlyClassCommentToKeywordFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\PhpdocReadonlyClassCommentToKeywordFixerTest <./../../../tests/Fixer/ClassNotation/PhpdocReadonlyClassCommentToKeywordFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
