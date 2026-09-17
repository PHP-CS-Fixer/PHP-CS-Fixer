==============================
Rule ``phpdoc_order_by_value``
==============================

Order PHPDoc tags by value.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``annotations``.

Configuration
-------------

``annotations``
~~~~~~~~~~~~~~~

List of annotations to order, e.g. ``["covers"]``.

Allowed values: a subset of ``['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'method', 'mixin', 'property', 'property-read', 'property-write', 'requires', 'throws', 'uses']``

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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocOrderByValueFixer <./../../../src/Fixer/Phpdoc/PhpdocOrderByValueFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocOrderByValueFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocOrderByValueFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
