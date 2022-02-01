==============================
Rule ``phpdoc_order_by_value``
==============================

Order phpdoc tags by value.

Configuration
-------------

``annotations``
~~~~~~~~~~~~~~~

List of annotations to order, e.g. ``["covers"]``.

Allowed values: a subset of ``['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'method', 'property', 'property-read', 'property-write', 'requires', 'throws', 'uses']``

Default value: ``['covers']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

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

Example #2
~~~~~~~~~~

With configuration: ``['annotations' => ['author']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   + * @author Alice
     * @author Bob
   - * @author Alice
     */
    final class MyTest extends \PHPUnit_Framework_TestCase
    {}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_order_by_value`` rule with the default config.
