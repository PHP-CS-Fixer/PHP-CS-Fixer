================================
Rule ``php_unit_ordered_covers``
================================

Order ``@covers`` annotation of PHPUnit tests.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
    /**
   + * @covers Bar
     * @covers Foo
   - * @covers Bar
     */
    final class MyTest extends \PHPUnit_Framework_TestCase
    {}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``php_unit_ordered_covers`` rule.
