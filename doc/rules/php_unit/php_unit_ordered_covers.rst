================================
Rule ``php_unit_ordered_covers``
================================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``phpdoc_order_by_value`` instead.

Order ``@covers`` annotation of PHPUnit tests.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   + * @covers Bar
     * @covers Foo
   - * @covers Bar
     */
    final class MyTest extends \PHPUnit_Framework_TestCase
    {}
