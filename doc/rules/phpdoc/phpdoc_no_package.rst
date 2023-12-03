==========================
Rule ``phpdoc_no_package``
==========================

``@package`` and ``@subpackage`` annotations should be omitted from PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @internal
   - * @package Foo
   - * subpackage Bar
     */
    class Baz
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoPackageFixer <./../../../src/Fixer/Phpdoc/PhpdocNoPackageFixer.php>`_
