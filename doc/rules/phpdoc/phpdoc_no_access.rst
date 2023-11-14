=========================
Rule ``phpdoc_no_access``
=========================

``@access`` annotations should be omitted from PHPDoc.

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

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoAccessFixer <./../src/Fixer/Phpdoc/PhpdocNoAccessFixer.php>`_
