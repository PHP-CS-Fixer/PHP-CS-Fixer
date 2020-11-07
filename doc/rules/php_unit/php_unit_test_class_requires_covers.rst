============================================
Rule ``php_unit_test_class_requires_covers``
============================================

Adds a default ``@coversNothing`` annotation to PHPUnit test classes that have
no ``@covers*`` annotation.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,7 @@
    <?php
   +
   +/**
   + * @coversNothing
   + */
    final class MyTest extends \PHPUnit_Framework_TestCase
    {

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``php_unit_test_class_requires_covers`` rule.
