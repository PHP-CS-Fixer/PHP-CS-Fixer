=========================
Rule ``phpdoc_list_type``
=========================

PHPDoc ``list`` type must be used instead of ``array`` without a key.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when ``array`` key should be present, but is missing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param array<int> $x
   - * @param array<array<string>> $y
   + * @param list<int> $x
   + * @param list<list<string>> $y
     */
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocListTypeFixer <./../../../src/Fixer/Phpdoc/PhpdocListTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocListTypeFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocListTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
