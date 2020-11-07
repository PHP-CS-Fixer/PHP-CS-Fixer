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
   @@ -1,9 +1,7 @@
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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_no_package`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_no_package`` rule.
