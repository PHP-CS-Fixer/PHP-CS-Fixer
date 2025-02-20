=========================
Rule ``phpdoc_no_access``
=========================

``@access`` annotations must be removed from PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
        /**
         * @internal
   -     * @access private
         */
        private $bar;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoAccessFixer <./../../../src/Fixer/Phpdoc/PhpdocNoAccessFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoAccessFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocNoAccessFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
